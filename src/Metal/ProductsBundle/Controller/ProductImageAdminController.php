<?php

namespace Metal\ProductsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProductsBundle\Form\ProductImageAdminType;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ProductImageAdminController extends CRUDController
{
    public function createMultipleAction()
    {
        $request = $this->admin->getRequest();

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $company = null;
        $options = array();
        if ($companyId = $request->get('id')) {
            $company = $em->getRepository('MetalCompaniesBundle:Company')->find($companyId);
        } else {
            $options = array(
                'need_select_category' => true,
                'entity_manager' => $em,
            );
        }

        $form = $this->createForm(new ProductImageAdminType(), null, $options);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if (!$form->isValid()) {
                return $this->render(
                    '@MetalProducts/ProductImageAdmin/product_image_multiple_create.html.twig',
                    array(
                        'form' => $form->createView(),
                        'object' => null,
                        'action' => null
                    )
                );
            }

            $images = array_filter($form->get('images')->getData());
            /* @var $images UploadedFile[]  */

            $category = null;
            if ($form->has('category')) {
                $category = $form->get('category')->getData();
                /* @var $category Category  */
            }

            if ($category && !$category->getAllowProducts()) {
                $this->addFlash(
                    'error',
                    sprintf('К категории %s нельзя прикреплять фотографии.', $category->getTitle())
                );

                return new RedirectResponse($this->admin->generateUrl('create_multiple'));
            }

            foreach ($images as $i => $image) {
                $productImage = new ProductImage();
                $productImage->setUploadedPhoto($image);

                if ($company) {
                    $productImage->setDescription(pathinfo($productImage->getUploadedPhoto()->getClientOriginalName(), PATHINFO_FILENAME));
                    $productImage->setCompany($company);
                } else {
                    $productImage->setCategory($category);
                }

                $em->persist($productImage);
            }

            $em->flush();

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->render('@MetalProducts/ProductImageAdmin/product_image_multiple_create.html.twig', array(
                'form' => $form->createView(),
                'object' => null,
                'action' => null
            ));
    }

    public function downloadFileAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $object = $this->admin->getObject($id);
        /* @var $object ProductImage */

        return $this->get('vich_uploader.download_handler')
            ->downloadObject($object, 'uploadedPhoto', null, true);

    }
}
