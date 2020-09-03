<?php

namespace Metal\NewsletterBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\NewsletterBundle\Entity\NewsletterViewer;
use Metal\NewsletterBundle\Entity\Recipient;
use Metal\ProjectBundle\Http\TransparentPixelResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class NewsletterViewerController extends Controller
{
    /**
     * @ParamConverter("recipient", class="MetalNewsletterBundle:Recipient", options={"mapping": {"hashKey": "hashKey"}})
     */
    public function trackAction(Recipient $recipient)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $newsletterViewer = $em->getRepository('MetalNewsletterBundle:NewsletterViewer')->findOneBy(
            array('recipient' => $recipient)
        );

        if (!$newsletterViewer) {
            $newsletterViewer = new NewsletterViewer();
            $newsletterViewer->setRecipient($recipient);

            $em->persist($newsletterViewer);
            $em->flush();
        }

        return new TransparentPixelResponse();
    }
}
