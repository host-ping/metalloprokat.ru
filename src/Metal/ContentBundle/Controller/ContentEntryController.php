<?php

namespace Metal\ContentBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\Question;
use Metal\ContentBundle\Entity\Topic;
use Metal\ContentBundle\Form\ContentEntryType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraint;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ContentEntryController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addAction(Request $request, $entry_type)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = null;
        $entry = null;
        if ($this->isGranted('ROLE_USER')) {
            $user = $this->getUser();
        }

        if ($entry_type === 'ENTRY_TYPE_QUESTION') {
            $entry = new Question();
        } elseif ($entry_type === 'ENTRY_TYPE_TOPIC') {
            $entry = new Topic();
        }

        $form = $this->createForm(
            new ContentEntryType(),
            $entry,
            array(
                'is_authenticated' => $user !== null,
                'validation_groups' => $user !== null ?
                    array(Constraint::DEFAULT_GROUP, 'authenticated') :
                    array(Constraint::DEFAULT_GROUP, 'anonymous'),
            )
        );

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        if ($user) {
            $entry->setUser($user);
        }

        $em->persist($entry);
        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
            )
        );
    }
}
