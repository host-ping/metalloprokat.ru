<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\CompaniesBundle\Entity\Company;
use Metal\PrivateOfficeBundle\Helper\SerializerHelper;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProductsBundle\Form\ProductType;
use Metal\ProductsBundle\Indexer\Operation\ProductChangeSet;
use Metal\ProductsBundle\Indexer\Operation\ProductsCriteria;
use Metal\ProjectBundle\Helper\FormattingHelper;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PrivateProductController extends Controller
{
    /**
     * @ParamConverter("product", class="MetalProductsBundle:Product", isOptional=true)
     * @Security("has_role('ROLE_SUPPLIER') and user.getHasEditPermission() and has_role('ROLE_CONFIRMED_EMAIL') and (not product or is_granted('COMPANY_MODERATOR', product.getCompany()))")
     */
    public function saveAction(Request $request, Product $product = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $company = $this->getUser()->getCompany();
        /* @var $company Company */

        $productRepository = $em->getRepository('MetalProductsBundle:Product');
        $maxProductsCount = $productRepository->getAvailableAddProductsCountToCompany($company);

        $categoryService = $this->get('metal.categories.category_matcher');

        $hasProduct = $product !== null;
        if (!$product) {
            $product = new Product();
        }

        $form = $this->createForm(new ProductType(), $product,
            array(
                'existing_product_editing' => $hasProduct,
                'company_id' => $company->getId()
            )
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array('errors' => $errors),
                200,
                // set Content-Type header for IE
                array('Content-Type' => 'text/plain')
            );
        }

        if (null !== $maxProductsCount && $maxProductsCount <= 0 && !$hasProduct) {
            $errors = array(
                $form->get('title')->createView()->vars['full_name'] => array(
                    sprintf('Полный и расширенный пакеты позволяют добавлять более %d товаров.', $company->getPackageChecker()->getMaxAvailableProductsCount())
                )
            );

            return JsonResponse::create(
                array('errors' => $errors),
                200,
                // set Content-Type header for IE
                array('Content-Type' => 'text/plain')
            );
        }

        if (!$product->getCategory()) {
            $possibleCategory = $categoryService->getCategoryByTitle($product->getTitle());
            $product->setCategory($possibleCategory);
            $product->setChecked(Product::STATUS_NOT_CHECKED);
        }

        $product->setCompany($company);
        if ($product->getId()) {
            $product->getProductLog()->setUpdatedBy($this->getUser());
        } else {
            $product->getProductLog()->setCreatedBy($this->getUser());
        }

        $productsChangeSet = new ProductsBatchEditChangeSet();

        if ($hasProduct) {
            $unitOfWork = $em->getUnitOfWork();
            $original = $unitOfWork->getOriginalEntityData($product);
            $unitOfWork->computeChangeSet($em->getClassMetadata(get_class($product)), $product);
            $diff = $unitOfWork->getEntityChangeSet($product);
            $unitOfWork->setOriginalEntityData($product, $original);

            $fieldsToResetStatus = array('title', 'size', 'category');
            $hasChangesForResetModeration = (bool) array_intersect(array_keys($diff), $fieldsToResetStatus);

            if ($hasChangesForResetModeration) {
                if (isset($diff['title'])) {
                    $possibleCategory = $categoryService->getCategoryByTitle($product->getTitle());
                    $product->setCategory($possibleCategory);
                }

                $product->setChecked(Product::STATUS_NOT_CHECKED);

                $productsChangeSet->productsToDisable = array($product->getId());
            } elseif (!empty($diff)) {
                $productsChangeSet->productsToEnable = array($product->getId());
            }
        }

        $product->setCurrency($company->getCountry()->getCurrency());
        $product->setUpdated();

        $productPhoto = null;
        if ($form->has('image') && $form->get('image')->getData()->getUploadedPhoto()) {
            $productPhoto = $form->get('image')->getData();
            $productPhoto->setCompany($company);
            $productPhoto->setDescription($product->getTitle());
            $product->setImage($productPhoto);

            $em->persist($productPhoto);
        }

        if (!$hasProduct) {
            $em->persist($product);
        }

        $company->getCounter()->setProductsUpdatedAt(new \DateTime());

        $em->flush();

        if ($productsChangeSet->productsToDisable || $productsChangeSet->productsToEnable) {
            $this->get('sonata.notification.backend')->createAndPublish('admin_products', array('changeset' => $productsChangeSet));
        }

        $em->getRepository('MetalStatisticBundle:StatsProductChange')->insertProductChanges($company->getId(), $product->getId(), !$hasProduct);
        if (!$hasProduct) {
            $em->getRepository('MetalCompaniesBundle:CompanyCounter')->changeCounter($company->getId(), array('allProductsCount'));
        }

        /** @var  SerializerHelper $serializerHelper */
        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');

        /** @var FormattingHelper $formattingHelper */
        $formattingHelper = $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');

        $productPhotoSerialized = null;
        if ($productPhoto) {
            $productPhotoSerialized = $serializerHelper->serializeProductPhoto($productPhoto);
        }

        $changeSet = new ProductChangeSet();
        //TODO: здесь дублируется логика - мы и так выше вызываем consumer. Есть ли смысл выполнять и это обновление?
        //TODO: Добавить price
        //TODO: не посылать обновление если продукт не был промодерирован
        $changeSet->setIsSpecialOffer($product->getIsSpecialOffer());

        $criteria = new ProductsCriteria();
        $criteria->addId($product->getId());

        $this->get('metal_products.indexer.products')->update($changeSet, $criteria);

        return JsonResponse::create(
            array(
                'status' => 'success',
                'product' => $serializerHelper->serializeProduct($product),
                'productsUpdatedAt' => $formattingHelper->formatDateTime($company->getCounter()->getProductsUpdatedAt()),
                'uploadedPhoto' => $productPhotoSerialized
            ),
            200, // set Content-Type header for IE
            array('Content-Type' => 'text/plain')
        );
    }
}
