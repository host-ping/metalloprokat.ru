<?php

namespace Metal\ContentBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\ContentImage;
use Metal\ContentBundle\Entity\ContentImageAlbum;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AlbumAdminController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction()
    {
        $user = $this->getUser();
        /* @var $user User */
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $albums = $em->getRepository('MetalContentBundle:ContentImageAlbum')->findBy(array('user' => $user));
        $em->getRepository('MetalContentBundle:ContentImage')->attachImagesToAlbums($albums);

        return $this->render(
            '@MetalContent/Images/list_albums.html.twig',
            array(
                'albums' => $albums,
                'admin_pool' => $this->container->get('sonata.admin.pool'),
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function createAlbumAction(Request $request)
    {
        $user = $this->getUser();
        /* @var $user User */
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $album = new ContentImageAlbum();
        $album->setTitle($request->request->get('title'));
        $album->setUser($user);

        $em->persist($album);
        $em->flush();

        return JsonResponse::create(array('status' => 'success'));
    }

    /**
     * @ParamConverter("album", class="MetalContentBundle:ContentImageAlbum")
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadPhotoAction(Request $request, ContentImageAlbum $album)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $photo = new ContentImage();
        $photo->setUploadedImage($request->files->get('image'));
        $photo->setUser($this->getUser());
        $photo->setAlbum($album);

        $em->persist($photo);
        $em->flush();

        return $this->redirect($this->generateUrl('MetalContentBundle:AlbumAdmin:list'));
    }
}
