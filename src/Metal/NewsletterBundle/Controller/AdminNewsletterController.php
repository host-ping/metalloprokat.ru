<?php

namespace Metal\NewsletterBundle\Controller;

use Metal\NewsletterBundle\Entity\Newsletter;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

class AdminNewsletterController extends CRUDController
{
    public function batchActionImportSubscribers(ProxyQueryInterface $selectedModelQuery)
    {
        $newsletters = $selectedModelQuery->execute();
        /* @var $newsletters Newsletter[] */

        foreach ($newsletters as $newsletter) {
            $this->getDoctrine()->getConnection()
                ->executeUpdate('INSERT INTO newsletter_recipient (subscriber_id, newsletter_id)
            SELECT s.ID, :newsletter_id
            FROM
              UserSend s
              LEFT JOIN newsletter_recipient nr
           ON nr.newsletter_id = :newsletter_id AND nr.subscriber_id = s.ID
            WHERE nr.id IS NULL AND s.subscribed_on_news = :status
              ', array('newsletter_id' => $newsletter->getId(), 'status' => true));
            ;

            $this->getDoctrine()->getRepository('MetalNewsletterBundle:Newsletter')->updateProcessedAt();
        }

        $this->addFlash('sonata_flash_success', 'Импорт выполнен');

        return $this->redirect($this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters())));
    }
}
