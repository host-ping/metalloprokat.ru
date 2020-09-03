<?php

namespace Metal\SupportBundle\Controller;

use Metal\PrivateOfficeBundle\Form\AddAnswerType;
use Metal\SupportBundle\Entity\Answer;
use Metal\SupportBundle\Entity\Topic;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class TopicAdminController extends CRUDController
{
    public function createAction($id = null)
    {
        if (!$id) {
            $this->addFlash('sonata_flash_error', 'Сначала нужно выбрать компанию из списка компаний и нажать "Создать обращение к сотруднику компании"');

            return new RedirectResponse(
                $this->container->get('router')->generate('admin_metal_companies_company_list')
            );
        }

        return parent::createAction();
    }

    public function showAction($id = null)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $id = $this->get('request_stack')->getMasterRequest()->get($this->admin->getIdParameter());

        $em = $this->getDoctrine()->getManager();
        $object = $this->admin->getObject($id);

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id : %s', $id));
        }

        if (false === $this->admin->isGranted('VIEW', $object)) {
            throw new AccessDeniedException();
        }

        $this->admin->setSubject($object);

        /* @var $object Topic */

        $answer = new Answer();
        $form = $this->createForm(new AddAnswerType(), $answer);

        if ($request->isMethod('POST')) {
            if ($request->request->get('resolve')) {
                $object->setResolvedBy($this->getUser());
                $object->setResolved(true);
                $this->admin->getModelManager()->update($object);
                $this->addFlash('success', 'Обращение в службу помечено как решенное.');
                $em->flush();

                return new RedirectResponse($this->admin->generateUrl('show', array('id' => $object->getId())));
            }

            if ($request->request->get('reopen')) {
                $object->setResolved(false);
                $this->admin->getModelManager()->update($object);
                $this->addFlash('success', 'Обращение в службу помечено как переоткрытое.');
                $em->flush();

                return new RedirectResponse($this->admin->generateUrl('show', array('id' => $object->getId())));
            }

            $form->handleRequest($request);
            if (count($form->getErrors(true))) {
                $this->addFlash('sonata_flash_error', 'При сохранении формы произошла ошибка');
            }

            if ($form->isValid()) {
                if ($request->request->get('add_answer_and_resolve')) {
                    $object->setResolvedBy($this->getUser());
                    $object->setResolved(true);
                    $this->admin->getModelManager()->update($object);
                    $this->addFlash('success', 'Обращение в службу помечено как решенное.');
                    $em->flush();
                }

                $answer->setTopic($object);
                $answer->setAuthor($this->getUser());
                $object->setLastAnswer($answer);

                $em->persist($answer);
                $em->flush();

                $email = '';
                $userName = '';
                $title = '';

                if($object->getEmail()) {
                    $email = $object->getEmail();
                    $userName = $object->getUserName();
                    $title = 'на сообщение из формы обратной связи.';
                } elseif ($object->getReceiver()) {
                    $email = $object->getReceiver()->getEmail();
                    $userName = $object->getReceiver()->getFullName();
                    $title = 'на Ваше обращение в службу техподдержки.';
                }

                if ($email) {
                    $mailer = $this->container->get('metal.newsletter.mailer');
                    try {
                        $mailer->sendMessage('@MetalSupport/emails/answer.html.twig',
                            $email,
                            array(
                                'userName' => $userName,
                                'title'    => $title,
                                'object'   => $answer,
                                'country'  => null
                            ));
                    } catch (\Swift_RfcComplianceException $e) {

                    }
                }

                if ($object->getAuthor()) {
                    $em->getRepository('MetalUsersBundle:UserCounter')->updateUsersCounters(
                        array($object->getAuthor()->getId()), array('new_moderator_answers')
                    );
                }

                $em->getRepository('MetalSupportBundle:Topic')->updateAnswersCount(
                    array('topic_ids' => array($object->getId()))
                );
                $this->addFlash('success', 'Ответ успешно добавлен.');
                return new RedirectResponse($this->admin->generateUrl('show', array('id' => $object->getId())));
            }
        }

        $formView = $form->createView();

        // set the theme for the current Admin Form
        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render(
            $this->admin->getTemplate('show'),
            array(
                'action' => 'show',
                'object' => $object,
                'form' => $formView,
                'elements' => $this->admin->getShow(),
            )
        );
    }
}
