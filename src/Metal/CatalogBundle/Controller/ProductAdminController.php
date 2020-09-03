<?php

namespace Metal\CatalogBundle\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\CatalogBundle\Entity\Product;
use Metal\CatalogBundle\Form\ProductsBatchEditType;
use Metal\CategoriesBundle\Entity\Category;
use Metal\TerritorialBundle\Entity\City;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductAdminController extends CRUDController
{
    public function loadAttributesByCategoryAction(Request $request)
    {
        $categoryId = $request->request->get('category_id');
        $attrValCatRepo = $this->getDoctrine()->getRepository('MetalAttributesBundle:AttributeValueCategory');

        $manufacturers = $attrValCatRepo->getAttributesOptionsArray(Product::ATTR_CODE_MANUFACTURER, $categoryId);
        $brands = $attrValCatRepo->getAttributesOptionsArray(Product::ATTR_CODE_BRAND, $categoryId);

        // transform [1 => a] to [[id => 1, label => a]]
        $normalizer = function(array $items) {
            $normalizedItems = array();

            foreach ($items as $id => $label) {
                $normalizedItems[] = array('id' => $id, 'label' => $label);
            }

            return $normalizedItems;
        };

        $manufacturers = $normalizer($manufacturers);
        $brands = $normalizer($brands);

        return JsonResponse::create(array('manufacturers' => $manufacturers, 'brands' => $brands));
    }

    public function loadAdditionalAttributesByCategoryAction(Request $request)
    {
        $categoryId = $request->request->get('category_id');
        $categoryRepo = $this->getDoctrine()->getRepository('MetalCategoriesBundle:Category');

        $object = new Product();
        $object->setCategory($categoryRepo->find($categoryId));
        $this->admin->setSubject($object);

        /* @var $form \Symfony\Component\Form\Form */
        $form = $this->admin->getForm();
        $form->setData($object);
        $formView = $form->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFormTheme());

        return $this->render(
            '@MetalCatalog/AdminProduct/additionalAttributes.html.twig',
            array(
                'admin' => $this->admin,
                'form_group' => $this->admin->getFormGroups()['AdditionalAttributes'],
                'form' => $formView
            )
        );
    }

    public function batchActionEdit(ProxyQueryInterface $query)
    {
        $request = $this->admin->getRequest();

        $products = $query->execute();
        /* @var $products Product[] */

        $form = $this->createForm(new ProductsBatchEditType());

        if (!$request->request->get('submitted')) {
            return $this->render(
                'MetalCatalogBundle:AdminProduct:batch_action_edit.html.twig',
                array(
                    'form' => $form->createView(),
                    'object' => null,
                    'action' => null
                )
            );
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->render(
                'MetalCatalogBundle:AdminProduct:batch_action_edit.html.twig',
                array(
                    'form' => $form->createView(),
                    'object' => null,
                    'action' => null,
                )
            );
        }

        $isFieldEditable = function ($field) use ($form) {
            return $form->has($field) && $form->get($field.'Editable')->getData();
        };

        if ($isFieldEditable('productCities')) {
            $cities = $form->get('productCities')->getData();
            /* @var $cities City[] */
            $this->productsCitiesEdit($products, $cities);
        }

        if ($isFieldEditable('category')) {
            $category = $form->get('category')->getData();
            /* @var $category Category */
            $this->productsCategoryEdit($products, $category);
        }

        return new RedirectResponse($this->generateUrl('admin_metal_catalog_product_list'));
    }

    /**
     * @param Product[] $products
     * @param Category $category
     *
     * @return null
     */
    private function productsCategoryEdit(array $products, Category $category)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $brands = $em->createQueryBuilder()
            ->select('IDENTITY(product.brand) AS brandId')
            ->from('MetalCatalogBundle:Product', 'product')
            ->where('product.category = :category')
            ->setParameter('category', $category)
            ->groupBy('product.brand')
            ->getQuery()
            ->getArrayResult()
        ;

        $isAllowedChangeCategory = true;
        if (!$brands) {
            $isAllowedChangeCategory = false;
            $this->addFlash('sonata_flash_error', sprintf('Для категории "%s" нет брендов.', $category->getTitle()));
        }

        $manufacturers = $em->createQueryBuilder()
            ->select('IDENTITY(product.manufacturer) AS manufacturerId')
            ->from('MetalCatalogBundle:Product', 'product')
            ->where('product.category = :category')
            ->setParameter('category', $category)
            ->groupBy('product.manufacturer')
            ->getQuery()
            ->getArrayResult()
        ;

        if (!$manufacturers) {
            $isAllowedChangeCategory = false;
            $this->addFlash('sonata_flash_error', sprintf('Для категории "%s" нет производителей.', $category->getTitle()));
        }

        if (false === $isAllowedChangeCategory) {
            return null;
        }

        $brandsByCategory = array();
        foreach ($brands as $brand) {
            $brandsByCategory[$brand['brandId']] = true;
        }

        $manufacturersByCategory = array();
        foreach ($manufacturers as $manufacturer) {
            $manufacturersByCategory[$manufacturer['manufacturerId']] = true;
        }

        $badProducts = array();
        $i = 0;
        $errors = array();
        foreach ($products as $product) {
            if (!array_key_exists($product->getBrand()->getId(), $brandsByCategory)) {
                $badProducts[$product->getId()] = true;
                $errors['brands'][] = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    $this->admin->generateUrl('edit', array('id' => $product->getId())),
                    $product->getId()
                );
            }

            if (!array_key_exists($product->getManufacturer()->getId(), $manufacturersByCategory)) {
                $badProducts[$product->getId()] = true;
                $errors['manufacturers'][] = sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    $this->admin->generateUrl('edit', array('id' => $product->getId())),
                    $product->getId()
                );
            }

            if (array_key_exists($product->getId(), $badProducts)) {
                continue;
            }

            $product->setCategory($category);
            $i++;
        }

        if (isset($errors['brands'])) {
            $this->addFlash(
                'sonata_flash_error',
                sprintf(
                    'К продуктам %s нельзя прикрепить категорию "%s" у неё нет текущего бренда.',
                    implode(', ', $errors['brands']),
                    $category->getTitle()
                )
            );
        }

        if (isset($errors['manufacturers'])) {
            $this->addFlash(
                'sonata_flash_error',
                sprintf(
                    'К продуктам %s нельзя прикрепить категорию "%s" у неё нет текущего производителя продукта.',
                    implode(', ', $errors['manufacturers']),
                    $category->getTitle()
                )
            );
        }

        $em->flush();

        $this->addFlash('sonata_flash_success', sprintf('Обновлена категория: "%s" для %d продуктов.', $category->getTitle(), $i));
    }

    /**
     * @param Product[] $products
     * @param City[] $cities
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function productsCitiesEdit(array $products, array $cities)
    {
        $conn = $this->getDoctrine()->getConnection();
        /* @var $conn Connection */
        $productsIds = array();
        foreach ($products as $product) {
            $productsIds[] = $product->getId();
        }

        $conn->executeUpdate(
            'DELETE FROM catalog_product_city WHERE product_id IN (:products_ids)',
            array('products_ids' => $productsIds), array('products_ids' => Connection::PARAM_INT_ARRAY)
        );

        $citiesTitles = array();
        foreach ($cities as $city) {
            $citiesTitles[] = $city->getTitle();
            foreach ($productsIds as $productId) {
                $conn->executeQuery(
                    'INSERT INTO catalog_product_city (product_id, city_id, created_at, updated_at) VALUES (:product_id, :city_id, NOW(), NOW())',
                    array('product_id' => $productId, 'city_id' => $city->getId()),
                    array('product_id' => \PDO::PARAM_INT, 'city_id' => \PDO::PARAM_INT)
                );
            }
        }

        $this->addFlash('sonata_flash_success', sprintf('Обновлены города: "%s" для "%d" продуктов.', implode(', ', $citiesTitles), count($productsIds)));
    }

    /**
     * {@inheritdoc}
     */
    protected function redirectTo($object)
    {
        $request = $this->get('request_stack')->getMasterRequest();
        if (null !== $request->get('btn_create_and_create')) {
            /* @var $object Product */
            $params = array(
                'previous_product_id' => $object->getId()
            );

            if ($this->admin->hasActiveSubClass()) {
                $params['subclass'] = $request->get('subclass');
            }

            $url = $this->admin->generateUrl('create', $params);

            return new RedirectResponse($url);
        }

        return parent::redirectTo($object);
    }
}
