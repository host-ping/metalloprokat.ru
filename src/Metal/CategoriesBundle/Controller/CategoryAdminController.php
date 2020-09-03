<?php

namespace Metal\CategoriesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Service\ProductCategoryDetector;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CategoryAdminController extends CRUDController
{
    public function showTreeAction()
    {
        $em = $this->getDoctrine()->getManager();
        /* @var EntityManager $em */

        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        $attributeCategoryRepository = $em->getRepository('MetalAttributesBundle:AttributeCategory');
        $categories = $categoryRepository->findBy(
            array(),
            array(
                'priority' => 'ASC',
                'title' => 'ASC'
            )
        );

        $attributesCategory = $attributeCategoryRepository
            ->createQueryBuilder('attributeCategory')
            ->select('IDENTITY(attributeCategory.category) AS category_id')
            ->addSelect('attribute.title AS attribute_title')
            ->join('attributeCategory.attribute', 'attribute')
            ->orderBy('attribute.title', 'ASC')
            ->getQuery()
            ->getResult()
        ;

        $attributesByCategory = array();
        foreach ($attributesCategory as $attributeCategory) {
            $attributesByCategory[$attributeCategory['category_id']][] = $attributeCategory['attribute_title'];
        }

        /* @var Category[] $categories */
        $treeCategories = array();
        foreach ($categories as $category) {
            if (!empty($attributesByCategory[$category->getId()])) {
                $category->setAttribute('category_attributes_titles', $attributesByCategory[$category->getId()]);
            }

            if ($category->getParent()) {
                $treeCategories[$category->getParent()->getId()][] = $category;
            } else {
                $treeCategories[0][] = $category;
            }
        }

        return $this->render(
            '@MetalCategories/CategoryAdmin/showTree.html.twig',
            array(
                'treeCategories' => $treeCategories,
                'object' => null,
                'action' => 'show'
            )
        );
    }

    public function testExtendedPatternAction(Request $request)
    {
        $patternString = $request->request->get('patternString');
        $subjectsString = $request->request->get('subjectsString');

        $categoryService = $this->get('metal.categories.category_matcher');
        $language = $categoryService->getExpressionLanguage();

        $delimiter = '\r\n|\r|\n';
        $subjects = strtr($subjectsString, ProductCategoryDetector::$patternReplacements);
        $subjects = preg_split('/'.$delimiter.'/', $subjects);

        $badPatterns = array();
        foreach ($subjects as $subject) {
            $subject = mb_strtolower(preg_replace('/[\/%«»]/ui', '', $subject));
            $subject = preg_replace('/\s+/u', ' ', $subject);

            if (!$language->evaluate($patternString, array('title' => $subject, 'category_detector' => $categoryService))) {
                $badPatterns[] = array(
                    $subject
                );
            }
        }

        return JsonResponse::create($badPatterns);
    }
}
