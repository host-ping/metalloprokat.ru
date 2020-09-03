<?php

namespace Metal\ContentBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\AbstractContentEntry;
use Metal\ContentBundle\Entity\Comment;
use Metal\ContentBundle\Form\CommentType;
use Metal\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Validator\Constraint;

class CommentController extends Controller
{
    /**
     * @ParamConverter("contentEntry", class="MetalContentBundle:AbstractContentEntry")
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, AbstractContentEntry $contentEntry)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
            /* @var $user User */
        }

        $comment = new Comment();
        $form = $this->createForm(
            new CommentType(),
            $comment,
            array(
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
                'errors' => $errors,
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

        $comment->setContentEntry($contentEntry);
        $comment->setUser($user);

        $em->persist($comment);
        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success'
        ));
    }
}
