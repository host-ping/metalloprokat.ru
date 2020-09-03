<?php

namespace Metal\ComplaintsBundle\Admin\Block;

use Doctrine\ORM\EntityManager;
use Metal\ComplaintsBundle\Entity\AbstractComplaint;
use Metal\ComplaintsBundle\Entity\ValueObject\ComplaintTypeProvider;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;

class SpamComplaintBlockService extends AbstractAdminBlockService
{
    const MAX_RESULTS = 5;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var Pool
     */
    private $adminPool;

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setAdminPool(Pool $adminPool)
    {
        $this->adminPool = $adminPool;
    }

    public function execute(BlockContextInterface $blockContext, Response $response = null)
    {
        $conn = $this->em->getConnection();
        $complaintsToModerator = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.complaints.admin.complaint')->isGranted('LIST')) {
            $complaintsToModerator =
                $conn->fetchAll('
                    SELECT
                        DATE(dc.created_at) AS createdDate,
                        dc.id AS complaintId,
                        dc.body AS description,
                        dc.user_id AS userId,
                        CONCAT_WS(" ", u.ForumName, u.LastName) AS fullName,
                        CONCAT_WS(" ", demand_user.ForumName, demand_user.LastName) AS demandUserFullName,
                        d.user_id AS demandUserId,
                        d.person,
                        dc.complaint_type as complaintTypeId,
                        d.id as demandId
                    FROM complaint dc
                    JOIN User u ON dc.user_id = u.User_ID
                    JOIN demand d ON dc.demand_id = d.id
                    LEFT JOIN User demand_user ON d.user_id = demand_user.User_ID
                    WHERE dc.complaint_object_type = :complaint_object
                    AND dc.processed_at IS NULL
                    ORDER BY createdDate DESC
                    LIMIT :limit
                    ',
                    array(
                        'complaint_object' => AbstractComplaint::DEMAND_TYPE,
                        'limit' => self::MAX_RESULTS
                    ),
                    array('limit' => \PDO::PARAM_INT)
                );
        }

        return $this->renderResponse(
            '@MetalComplaints/ComplaintAdmin/Block/spam_complaints.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'data' => $complaintsToModerator,
                'isGranted' => $isGranted,
                'complaintTypes' => ComplaintTypeProvider::getAllTypesAsSimpleArrayWithoutKind()
            ),
            $response
        );
    }
}
