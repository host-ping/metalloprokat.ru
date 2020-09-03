<?php

namespace Metal\AttributesBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\AttributesBundle\Entity\AttributeValueCategory;
use Metal\AttributesBundle\Form\AttributeValueCategoryAdminType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AttributeValueCategoryAdminController extends CRUDController
{
    public function createAction()
    {
        $request = $this->admin->getRequest();

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $form = $this->createForm(new AttributeValueCategoryAdminType(), null, array('category_repository' => $em->getRepository('MetalCategoriesBundle:Category')));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                return $this->render('MetalAttributesBundle:Admin:attribute_value_category_create.html.twig', array(
                    'form' => $form->createView(),
                    'object' => null,
                    'action' => null
                ));
            }

            $categories = $form->get('categories')->getData();
            $attribute = $form->get('attribute')->getData();
            $possibleValues = $form->get('possibleValue')->getData();

            $possibleValues = preg_split('/(\n\r|\n|\r)/', $possibleValues);
            $possibleValues = array_values(array_unique(array_filter(array_map('trim', $possibleValues))));

            $attrValueCreated = 0;
            $attrValueCategoryCreated = 0;
            $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');
            $attributeValueCategoryRepository = $em->getRepository('MetalAttributesBundle:AttributeValueCategory');
            $slugify = $this->container->get('slugify');
            foreach ($possibleValues as $i => $possibleValue) {
                $attributeValue = $attributeValueRepository->findOrCreateAttributeValue($attribute, $possibleValue, $slugify);
                if (!$attributeValue->getId()) {
                    $attrValueCreated++;
                }

                foreach ($categories as $category) {
                    $attrValueCategory = $attributeValueCategoryRepository->findOrCreateAttributeValueCategory($attributeValue, $category);
                    if (!$attrValueCategory->getId()) {
                        $attrValueCategoryCreated++;
                    }
                }

                if ($i % 25 === 0) {
                    $em->flush();
                }
            }

            $em->flush();

            if ($attrValueCreated) {
                $this->addFlash('sonata_flash_success', 'Кол-во добавленых значений атрибутов: '.$attrValueCreated);
            }

            if ($attrValueCategoryCreated) {
                $this->addFlash('sonata_flash_success', 'Кол-во добавленых значений атрибутов в категориях: '.$attrValueCategoryCreated);
            }

            $em->getRepository('MetalAttributesBundle:AttributeCategory')->refreshAttributeCategory();

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('MetalAttributesBundle:Admin:attribute_value_category_create.html.twig', array(
            'form' => $form->createView(),
            'object' => null,
            'action' => null
        ));
    }
}
