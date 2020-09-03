<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class PrivateCustomCategoriesController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function productsAction()
    {
        $user = $this->getUser();
        /* @var $user User */

        $company = $user->getCompany();
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $companyCityRepository = $em->getRepository('MetalCompaniesBundle:CompanyCity');
        $customCompanyCategoryRepo = $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory');

        $categories = $customCompanyCategoryRepo
            ->findBy(array('company' => $company), array('displayPosition' => 'ASC'));
        $categories = $customCompanyCategoryRepo->serializeAndFlattenCategories($categories);

        return $this->render(
            '@MetalPrivateOffice/MiniSite/products.html.twig',
            array(
                'commonPhotos' => array(),
                'categories' => $categories,
                'company' => $company,
                'branches' => $companyCityRepository->getCompanyCitiesDataForCompany($company),
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getCompany().getPackageChecker().isCustomCategoriesAllowed() and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission()")
     */
    public function moveToCategoryAction(Request $request)
    {
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $categoryId = $request->request->get('category_id');
        $productsIds = $request->request->get('products');

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $category = $em->find('MetalCompaniesBundle:CustomCompanyCategory', $categoryId);
        $productRepository = $em->getRepository('MetalProductsBundle:Product');

        $products = $productRepository->moveToCategory($company, $category, $productsIds);


        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $em->flush();

        $productsBatchEditChangeSet = new ProductsBatchEditChangeSet();
        $productsBatchEditChangeSet->productsToEnable = array_keys($products);

        $this->container->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsBatchEditChangeSet));

        $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory')->calculateCanCompanyHasCustomCategory($company);

        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */

        return JsonResponse::create(array(
            'status' => 'success',
            'customCategory' => array(
                'id' => $category->getId(),
                'title' => $category->getTitle(),
            ),
            'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and (not request.isMethod('POST') or has_role('ROLE_CONFIRMED_EMAIL'))")
     */
    public function categoriesAction()
    {
        $user = $this->getUser();
        /* @var $user User */
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $user->getCompany();

        $customCompanyCategoryRepo = $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory');

        $categories = $customCompanyCategoryRepo
            ->findBy(array('company' => $company), array('displayPosition' => 'ASC'));
        $serializedCategories = $customCompanyCategoryRepo->serializeCategories($categories);

        return $this->render(
            '@MetalPrivateOffice/MiniSite/custom_categories.html.twig',
            array(
                'categories' => $serializedCategories,
                'company' => $company,
            )
        );
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER') and user.getCompany().getPackageChecker().isCustomCategoriesAllowed() and user.getHasEditPermission()")
     */
    public function saveCategoriesByLevelsAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $user->getCompany();

        $customCompanyCategoryRepo = $em->getRepository('MetalCompaniesBundle:CustomCompanyCategory');

        $flattenCategories = $customCompanyCategoryRepo->flattenCategoriesHierarchy($request->request->get('categories'));

        $constraints = new Assert\All(
            new Assert\Collection(
                array(
                    'allowExtraFields' => true,
                    'fields' => array('title' => array(new Assert\NotBlank(), new Assert\Length(array('min' => 2)))),
                )
            )
        );
        $violationsList = $this->get('validator')->validate($flattenCategories, $constraints);

        if (count($violationsList)) {
            return JsonResponse::create(
                array(
                    'status' => 'error',
                )
            );
        }

        $categories = $customCompanyCategoryRepo->getCategoriesForCompany($company);

        $updatedCategoriesIds = array();
        $sequenceToCategory = array();
        /* @var $sequenceToCategory CustomCompanyCategory[] */

        foreach ($flattenCategories as $flattenCategory) {
            if ($flattenCategory['id'] && isset($categories[$flattenCategory['id']])) {
                // обновление
                $category = $categories[$flattenCategory['id']];
                $updatedCategoriesIds[$flattenCategory['id']] = true;
            } else {
                // добавление
                $category = new CustomCompanyCategory();
                $category->setCompany($company);
                $em->persist($category);
            }
            // иерархия родитель-ребенок будет обработана позднее
            $category->setParent(null);
            $category->setTitle($flattenCategory['title']);
            $category->setDisplayPosition($flattenCategory['sequence']);

            $sequenceToCategory[$flattenCategory['sequence']] = $category;
        }

        // удаление
        $categoriesToDelete = array_diff_key($categories, $updatedCategoriesIds);
        foreach ($categoriesToDelete as $category) {
            $em->remove($category);
        }

        foreach ($flattenCategories as $flattenCategory) {
            $category = $sequenceToCategory[$flattenCategory['sequence']];
            if ($flattenCategory['parent_sequence']) {
                $category->setParent($sequenceToCategory[$flattenCategory['parent_sequence']]);
            }
        }

        $em->flush();

        if (!$flattenCategories) {
            $customCompanyCategoryRepo->calculateCanCompanyHasCustomCategory($company);
        }

        $customCompanyCategoryRepo->refreshDenormalizedData();

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }
}
