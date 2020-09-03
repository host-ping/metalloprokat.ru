<?php

namespace Metal\ProjectBundle\Controller;

use Metal\AttributesBundle\Entity\DTO\AttributeCollectionFinder;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\SeoTemplate;
use Metal\ProjectBundle\Entity\SeoTemplateAttribute;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Yaml\Yaml;

class SeoTemplateAdminController extends CRUDController
{
    public function importAction(Request $request)
    {
        $form = $this->createFormBuilder()
            ->add('feed', TextareaType::class, ['constraints' => new NotBlank()])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->processFeedImport($form->get('feed')->getData());

            return $this->redirect($this->admin->generateUrl('list'));
        }

        return $this->render(
            '@MetalProject/SeoTemplateAdmin/import.html.twig',
            [
                'action' => 'import',
                'object' => null,
                'form' => $form->createView()
            ]
        );
    }

    private function processFeedImport($feed)
    {
        try {
            $feed = Yaml::parse($feed);
        } catch (\Exception $e) {
            $this->addFlash('sonata_flash_error', $e->getMessage());
        }

        $seoTemplateRepo = $this->getDoctrine()->getRepository('MetalProjectBundle:SeoTemplate');
        $validator = $this->get('validator');
        $em = $this->getDoctrine()->getManager();

        $inserted = $updated = 0;
        foreach ((array)$feed as $i => $item) {
            if (!is_array($item)) {
                $this->addFlash(
                    'sonata_flash_error',
                    sprintf('Набор %d: Некорректно сформированная структура.', $i + 1)
                );

                continue;
            }

            $seoTemplate = new SeoTemplate();
            $this->bindDataToSeoTemplate($item, $seoTemplate);

            /** @var ConstraintViolationInterface[] $errors */
            $errors = $validator->validate($seoTemplate);

//            dump($item, $seoTemplate, $errors);
//            exit;

            if (count($errors)) {
                foreach ($errors as $error) {
                    $this->addFlash(
                        'sonata_flash_error',
                        sprintf('Набор %d (%s): "%s" %s', $i + 1, $seoTemplate->getName(), $error->getPropertyPath(), $error->getMessage())
                    );
                }

                continue;
            }

            $seoTemplate = $seoTemplateRepo->findOneBy(['name' => $seoTemplate->getName()]);

            if (!$seoTemplate) {
                $seoTemplate = new SeoTemplate();
            }

            $this->bindDataToSeoTemplate($item, $seoTemplate);

            if (!$seoTemplate->getId()) {
                $em->persist($seoTemplate);
                $inserted++;
            } else {
                $updated++;
            }

            if (($i % 50) === 0) {
                $em->flush();
            }
        }

        $em->flush();

        $this->addFlash('sonata_flash_success', sprintf('Создано новых шаблонов: %d', $inserted));
        $this->addFlash('sonata_flash_success', sprintf('Обновлено существующих шаблонов: %d', $updated));
    }

    private function bindDataToSeoTemplate($data, SeoTemplate $seoTemplate)
    {
        $keys = ['name', 'category', 'title', 'description', 'h1', 'text'];
        $data = array_replace(array_fill_keys($keys, ''), $data);
        $data['attributes'] = isset($data['attributes']) && is_array($data['attributes']) ? $data['attributes'] : [];
        $data['category'] = trim($data['category'], '/');

        $categoryRepo = $this->getDoctrine()->getRepository('MetalCategoriesBundle:Category');

        $seoTemplate->setName($data['name']);
        $seoTemplate->setTextBlock($data['text']);

        $category = $categoryRepo->findOneBy(['slugCombined' => $data['category']]);
        $seoTemplate->setCategory($category);

        $metadata = $seoTemplate->getMetadata();
        $metadata->setTitle($data['title']);
        $metadata->setDescription($data['description']);
        $metadata->setH1Title($data['h1']);

//        dump($seoTemplate);exit;

        if (!$category) {
            return;
        }

        $attributesCollectionFinder = $this->getAttributesCollectionFinder($category);

        $usedCodes = [];
        foreach ($data['attributes'] as $code => $value) {
            $seoTemplateAttribute = new SeoTemplateAttribute();

            if (!$attribute = $attributesCollectionFinder->findAttributeByCode($code)) {
                // специально добавляем пустой атрибут с некорректным значением, что б показать это на валидации
                $seoTemplate->addSeoTemplateAttribute($seoTemplateAttribute);
                continue;
            }

            if ($value !== true) {
                if (!$attributeValue = $attributesCollectionFinder
                    ->findAttributeValueByAttributeAndSlug($attribute, $value)) {
                    // специально добавляем пустой атрибут с некорректным значением, что б показать это на валидации
                    $seoTemplate->addSeoTemplateAttribute($seoTemplateAttribute);
                    continue;
                }

                $seoTemplateAttribute->setAttributeValue($attributeValue);
            }

            $seoTemplateAttribute->setAttribute($attribute);
            $seoTemplate->addSeoTemplateAttribute($seoTemplateAttribute);
            $usedCodes[$attribute->getCode()] = true;
        }

        foreach ($seoTemplate->getSeoTemplateAttributes() as $seoTemplateAttribute) {
            if ($seoTemplateAttribute->getAttribute()) {
                $code = $seoTemplateAttribute->getAttribute()->getCode();
                if (empty($usedCodes[$code])) {
                    $seoTemplate->removeSeoTemplateAttribute($seoTemplateAttribute);
                }
            }
        }
    }

    /**
     * @param Category $category
     *
     * @return AttributeCollectionFinder
     */
    private function getAttributesCollectionFinder(Category $category)
    {
        static $collections = [];

        $cacheId = $category->getRealCategoryId();
        if (isset($collections[$cacheId])) {
            return $collections[$cacheId];
        }

        $attributeValueRepo = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValue');

        $attributesCollection = $attributeValueRepo->getAttributesCollectionByCategory($category);
        $attributesCollectionFinder = new AttributeCollectionFinder($attributesCollection);

        return $collections[$cacheId] = $attributesCollectionFinder;
    }
}
