<?php

namespace Metal\ContentBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\InstagramComment;
use Metal\ContentBundle\Entity\InstagramPhoto;
use Metal\ContentBundle\Form\CommentType;
use Metal\UsersBundle\Entity\User;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;

class InstagramController extends Controller
{

    public $photosPerPage = 15;

    public function photosListAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $photosQb = $em->getRepository('MetalContentBundle:InstagramPhoto')->createQueryBuilder('p')
            ->select('p')
            ->orderBy('p.createdAt', 'DESC')
        ;

        $pagerfanta = new Pagerfanta(new DoctrineORMAdapter($photosQb, false));
        $pagerfanta
            ->setMaxPerPage($this->photosPerPage)
            ->setCurrentPage($request->query->get('page', 1));

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalContent/partial/instagram_photos_in_list.html.twig',
                array(
                    'pagerfanta' => $pagerfanta,
                )
            );
        }

        return $this->render('@MetalContent/Instagram/instagram_photos.html.twig',
            array(
                'pagerfanta' => $pagerfanta
            )
        );
    }

    /**
     * @ParamConverter("photo", class="MetalContentBundle:InstagramPhoto")
     */
    public function photoViewAction(InstagramPhoto $photo)
    {
        return $this->render('@MetalContent/Instagram/instagram_photo.html.twig',
            array(
                'photo' => $photo,
                'comments' => $this->getDoctrine()->getManager()->getRepository('MetalContentBundle:InstagramComment')->getCommentsByObject($photo)
            )
        );
    }

    /**
     * @ParamConverter("photo", class="MetalContentBundle:InstagramPhoto")
     * @Security("has_role('ROLE_USER')")
     */
    public function addCommentAction(Request $request, InstagramPhoto $photo)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }

        $comment = new InstagramComment();
        $form = $this->createForm(
            new CommentType(),
            $comment,
            array(
                'data_class' => InstagramComment::class,
                'is_authenticated' => $user !== null,
                'validation_groups' => $user !== null ?
                    array(Constraint::DEFAULT_GROUP, 'authenticated') :
                    array(Constraint::DEFAULT_GROUP, 'anonymous'),
                'user' => $user
            )
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $akismet = $this->container->get('ornicar_akismet');
        $isSpam = $akismet->isSpam(array(
            'comment_author' => $user ? $user->getFullName() : $comment->getName(),
            'comment_content' => $comment->getDescription()
        ));

        if ($isSpam) {
            return JsonResponse::create(array('errors' => array('metal_content_comment[description]' => array('Обнаружен спам.'))));
        }

        $comment->setInstagramPhoto($photo);
        $comment->setUser($user);

        $em->persist($comment);
        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success'
        ));
    }
}
