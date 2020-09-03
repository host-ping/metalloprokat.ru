<?php


namespace Metal\PrivateOfficeBundle\Controller;

use Doctrine\ORM\EntityManager;

use Metal\PrivateOfficeBundle\Form\AddTopicType;
use Metal\PrivateOfficeBundle\Form\AddAnswerType;
use Metal\SupportBundle\Entity\Answer;
use Metal\SupportBundle\Entity\Topic;

use Metal\UsersBundle\Entity\User;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class PrivateSupportController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function listAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $perPage = 20;

        $topicsQb = $em
            ->getRepository('MetalSupportBundle:Topic')
            ->getTopicsForUserQb($user)
            ->select('t');

        $pagerfanta = (new Pagerfanta(new DoctrineORMAdapter($topicsQb, false)))
            ->setMaxPerPage($perPage)
            ->setCurrentPage($request->query->get('page', 1));

        if ($request->isXmlHttpRequest()) {
            return $this->render(
                '@MetalPrivateOffice/partials/topics_list.html.twig',
                array(
                    'pagerfanta' => $pagerfanta
                )
            );
        }

        return $this->render('@MetalPrivateOffice/PrivateSupport/list.html.twig', array('pagerfanta' => $pagerfanta));
    }

    /**
     * @ParamConverter("topic", class="MetalSupportBundle:Topic")
     * @Security("is_granted('CAN_VIEW_OR_ANSWER_TOPIC', topic)")
     */
    public function viewAction(Topic $topic)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $topicRepository = $em->getRepository('MetalSupportBundle:Topic');
        $answerRepository = $em->getRepository('MetalSupportBundle:Answer');
        $userRepository = $em->getRepository('MetalUsersBundle:User');
        $userCounterRepository = $em->getRepository('MetalUsersBundle:UserCounter');
        $form = $this->createForm(new AddAnswerType());

        $user = $this->getUser();
        /* @var $user User */

        $answers = $answerRepository->findBy(array('topic' => $topic->getId()), array('createdAt' => 'ASC'));

        $previousAdmin = $this->isGranted('ROLE_PREVIOUS_ADMIN');
        if ($topic->getUnreadAnswersCount() && !$previousAdmin) {
            $answerRepository->markAnswersAsViewed($answers);
            $topicRepository->updateAnswersCount(array('topics_ids' => array($topic->getId())));
            $userCounterRepository->updateUsersCounters(array($user->getId()), array('new_moderator_answers'));
        }

        if (!$topic->getViewedAt() && !$previousAdmin) {
            $topic->setViewedAt();
            $em->flush();
        }

        return $this->render('@MetalPrivateOffice/PrivateSupport/view.html.twig', array(
            'topic' => $topic,
            'answers' => $answers,
            'form' => $form->createView()
        ));
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addTopicAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $user = $this->getUser();
        /* @var $user User */

        $topic = new Topic();

        $form = $this->createForm(new AddTopicType(), $topic);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $topic->setSentFrom(Topic::SOURCE_PRIVATE_OFFICE);
        $topic->setAuthor($user);
        $topic->setReceiver($user);

        $company = null;
        if ($this->isGranted('ROLE_SUPPLIER')) {
            $company = $user->getCompany();
            $topic->setCompany($company);
        }

        $em->persist($topic);
        $em->flush();

        if ($company && ($manager = $company->getManager())) {
            $mailer = $this->get('metal.newsletter.mailer');
            try {
                /* @var $manager User */
                $mailer->sendMessage(
                    '@MetalUsers/emails/duplicate_messages_on_email_administration.html.twig',
                    array($manager->getEmail() => $manager->getFullName()),
                    array(
                        'senderUserName' => $topic->getAuthor()->getFullName(),
                        'senderEmail' => $topic->getAuthor()->getEmail(),
                        'textMessage' => $topic->getDescription(),
                        'topicId' => $topic->getId(),
                        'country' => $company->getCountry(),
                        'isPrivate' => true
                    )
                );
            } catch (\Swift_RfcComplianceException $e) {

            }
        }

        return JsonResponse::create(array(
            'status' => 'success',
            'topic'  => array(
                'id' => $topic->getId(),
                'url' => $this->generateUrl('MetalPrivateOfficeBundle:Support:view', array('id' => $topic->getId())),
            )
        ));
    }

    /**
     * @ParamConverter("topic", class="MetalSupportBundle:Topic", options={"id" = "topic_id"})
     * @Security("is_granted('CAN_VIEW_OR_ANSWER_TOPIC', topic)")
     */
    public function addAnswerAction(Request $request, Topic $topic)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $answer = new Answer();

        $form = $this->createForm(new AddAnswerType(), $answer);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors
            ));
        }

        $user = $this->getUser();
        /* @var $user User */

        $answer->setTopic($topic);
        $answer->setAuthor($user);
        $topic->setLastAnswer($answer);
        $topic->setResolved(false);

        $em->persist($answer);
        $em->flush();

        $company = $user->getCompany();

        if ($company) {
            $mailer = $this->get('metal.newsletter.mailer');

            $contactToNotify = null;
            if ($topic->getSentFrom() == Topic::SOURCE_ADMIN) {
                $contactToNotify = array($topic->getAuthor()->getEmail() => $topic->getAuthor()->getFullName());
            } elseif ($manager = $company->getManager()) {
                $contactToNotify = array($manager->getEmail() => $manager->getFullName());
            }

            if ($contactToNotify) {
                $mailer->sendMessage(
                    '@MetalUsers/emails/duplicate_messages_on_email_administration.html.twig',
                    $contactToNotify,
                    array(
                        'senderUserName' => $user->getFullName(),
                        'senderEmail' => $user->getEmail(),
                        'textMessage' => $topic->getDescription(),
                        'topicId' => $topic->getId(),
                        'country' => $company->getCountry(),
                        'reopen' => true,
                        'isPrivate' => true
                    )
                );
            }
        }

        $topicRepository = $em->getRepository('MetalSupportBundle:Topic');
        $topicRepository->updateAnswersCount(array('topics_ids' => array($topic->getId())));

        return JsonResponse::create(array(
            'status' => 'success',
            'answerHtml' => $this->renderView('@MetalPrivateOffice/PrivateSupport/answer_in_answers.html.twig', array('answer' => $answer)),
        ));
    }
}
