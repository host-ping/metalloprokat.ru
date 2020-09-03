<?php

namespace Metal\SupportBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\SupportBundle\Entity\Topic;
use Metal\SupportBundle\Form\TopicCorpSiteType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TopicController extends Controller
{
    public function saveCorpSiteAction(Request $request)
    {
        $topic = new Topic();
        $topicForm = $this->createForm(new TopicCorpSiteType(), $topic);

        $topicForm->handleRequest($request);

        if (!$topicForm->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($topicForm);

            return JsonResponse::create(array(
                    'errors' => $errors,
                ));
        }

        $akismet = $this->container->get('ornicar_akismet');
        $isSpam = $akismet->isSpam(array(
                'comment_author' => $topic->getUserName(),
                'comment_content' => $topic->getDescription()
            ));

        if ($isSpam) {
            return JsonResponse::create(array('errors' => array('metal_topic_corp_site[description]' => array('Обнаружен спам.'))));
        }

        $topic->setSentFrom(Topic::SOURCE_CORPSITE);

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $em->persist($topic);
        $em->flush();

        $mailer = $this->get('metal.newsletter.mailer');
        $adminEmails = $this->container->getParameter('admin_emails_for_feedback');

        try {
            foreach ($adminEmails as $adminEmail) {
                $mailer->sendMessage('MetalUsersBundle::emails/duplicate_messages_on_email_administration.html.twig', $adminEmail,
                    array(
                        'senderUserName' => $topicForm->get('userName')->getData(),
                        'senderEmail' => $topicForm->get('email')->getData(),
                        'textMessage' => $topicForm->get('description')->getData(),
                        'topicId'  => $topic->getId(),
                        'country'     => null,
                        'isDuplicate' => true
                    ));
            }

            $user = $em->getRepository('MetalUsersBundle:User')->findOneBy(array('email' => $topic->getEmail()));
            if ($user && $user->getCompany() && ($manager = $user->getCompany()->getManager())) {
                $mailer->sendMessage(
                    '@MetalUsers/emails/duplicate_messages_on_email_administration.html.twig',
                    array($manager->getEmail() => $manager->getFullName()),
                    array(
                        'senderUserName' => $topicForm->get('userName')->getData(),
                        'senderEmail' => $topicForm->get('email')->getData(),
                        'textMessage' => $topicForm->get('description')->getData(),
                        'topicId' => $topic->getId(),
                        'country' => null
                    )
                );
            }
        } catch (\Swift_RfcComplianceException $e) {

        }

        return JsonResponse::create(array(
                'status' => 'success'
            ));

    }
}
