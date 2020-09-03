<?php


namespace Metal\NewsletterBundle\Controller;

use Metal\NewsletterBundle\Entity\Subscriber;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\Request;

class AdminSubscriberController extends CRUDController
{
    public function importSubscribersAction(Request $request)
    {
        if ($request->isMethod('POST')) {
            $emails = $request->request->get('emails');
            $emails = preg_split('/(\n\r|\n|\r)/', $emails);
            $emails = array_values(array_unique(array_filter(array_map('trim', $emails))));

            $em = $this->getDoctrine()->getManager();

            foreach ($emails as $i => $email) {
                if ($em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('email' => $email))) {
                    continue;
                }

                $subscriber = new Subscriber();
                $subscriber->setEmail($email);
                $subscriber->setSource($request->request->get('source'));

                $em->persist($subscriber);
                if ($i % 20 === 0) {
                    $em->flush();
                }
            }
            $em->flush();

            $request->getSession()->getFlashBag()->set(
                'success',
                'Успешно импортировано'
            );

            return $this->redirect($this->generateUrl($request->attributes->get('_route'), $request->attributes->get('_route_parameters', array())));
        }

        return $this->render('MetalNewsletterBundle:SubscriberAdmin:importSubscribers.html.twig');
    }
}
