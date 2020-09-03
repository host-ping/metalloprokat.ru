<?php

namespace Metal\AttributesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function loadAttributesByCategoryIdAction(Request $request)
    {
        return JsonResponse::create($this->getAttributes($request->query->get('category_id')));
    }

    public function getPossibleAttributesForTitleAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $categoryId = $request->query->get('category_id');
        $text = $request->query->get('text_area');
        $size = $request->query->get('size_area');
        $productParameterValueRepository = $em->getRepository('MetalProductsBundle:ProductParameterValue');

        $attributesForTitle = $productParameterValueRepository->matchAttributesForTitle($categoryId, $text, $size);
        $possibleAttributesValuesIds = array_column($attributesForTitle, 'parameterOptionId');

        return JsonResponse::create(
            array('possible' => $this->getAttributes($categoryId), 'selected' => $possibleAttributesValuesIds)
        );
    }

    private function getAttributes($categoryId)
    {
        if (!$categoryId) {
            return array();
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $category = $em->getRepository('MetalCategoriesBundle:Category')->find($categoryId);
        if (!$category) {
            return array();
        }

        $attributeValues = $em->getRepository('MetalAttributesBundle:AttributeValueCategory')
            ->getSimpleAttributeValuesForCategory($category);

        if (!$attributeValues) {
            return array();
        }

        $attributes = $em->getRepository('MetalAttributesBundle:Attribute')
            ->findBy(array('id' => array_keys($attributeValues)), array('outputPriority' => 'ASC'));

        $response = array();
        foreach ($attributes as $attribute) {
            $response[] = array(
                'id' => $attribute->getId(),
                'title' => $attribute->getTitle(),
                'values' => $attributeValues[$attribute->getId()]
            );
        }

        return $response;
    }
}
