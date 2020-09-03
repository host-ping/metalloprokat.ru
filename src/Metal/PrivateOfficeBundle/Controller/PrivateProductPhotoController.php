<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\PrivateOfficeBundle\Form\UploadProductsImagesType;
use Metal\PrivateOfficeBundle\Helper\SerializerHelper;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\UsersBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateProductPhotoController extends Controller
{
    /**
     * @ParamConverter("productPhoto", class="MetalProductsBundle:ProductImage", isOptional=true)
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission() and user.getCompany().getPackageChecker().isAllowedConnectWithPhoto() and (not productPhoto or productPhoto.getCompany().getId() == user.getCompany().getId())")
     */
    public function saveAction(Request $request, ProductImage $productPhoto = null)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */
        //TODO: по идее нужно писать пользователя в productImage
        $company = $user->getCompany();

        if (!$productPhoto) {
            $productPhoto = new ProductImage();
        }

        $form = $this->createForm(new UploadProductsImagesType(), $productPhoto);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors
                ),
                200,
                // set Content-Type header for IE
                array('Content-Type' => 'text/plain')
            );
        }

        $productPhoto->setCompany($company);

        if (!$productPhoto->getId()) {
            $em->persist($productPhoto);
        }
        $em->flush();

        $serializerHelper = $this->get('brouzie.helper_factory')->get('MetalPrivateOfficeBundle:Serializer');
        /* @var $serializerHelper SerializerHelper */

        return JsonResponse::create(
            array(
                'status' => 'success',
                'uploadedPhoto' => $serializerHelper->serializeProductPhoto($productPhoto),
            ),
            200,
            // set Content-Type header for IE
            array('Content-Type' => 'text/plain')
        );
    }

    /**
     * @ParamConverter("photo", class="MetalProductsBundle:ProductImage" , options={"id" = "photo_id"})
     * @Security("has_role('ROLE_SUPPLIER') and has_role('ROLE_CONFIRMED_EMAIL') and user.getHasEditPermission() and (photo.getId() and photo.getCompany().getId() == user.getCompany().getId())")
     */
    public function deleteAction(ProductImage $photo)
    {
        //TODO: handle csrf
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $this->get('vich_uploader.upload_handler')->remove($photo, 'uploadedPhoto');
        $em->remove($photo);
        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }
}
