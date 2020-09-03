<?php

namespace Metal\CompaniesBundle\Admin\Block;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManager;
use Sonata\AdminBundle\Admin\Pool;
use Sonata\BlockBundle\Block\Service\AbstractAdminBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;

class UnprocessedCompanyDocumentBlockService extends AbstractAdminBlockService
{
    const MAX_RESULTS = 10;

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
        $unprocessedCompanyDocuments = array();
        if ($isGranted = $this->adminPool->getAdminByAdminCode('metal.companies.admin.company')->isGranted('LIST')) {
            $currentModerator = $this->adminPool->getContainer()->get('security.token_storage')->getToken()->getUser();
            $unprocessedCompanyDocuments = $this->em->getRepository('MetalCompaniesBundle:PaymentDetails')
                ->createQueryBuilder('pd')
                ->select('pd')
                ->addSelect('c')
                ->addSelect('m')
                ->where('(pd.uploadedAt > pd.approvedAt OR pd.approvedAt IS NULL)')
                ->andWhere('pd.uploadedAt IS NOT NULL')
                ->join('pd.company', 'c')
                ->join('c.manager', 'm')
                ->andWhere('c.manager = :manager')
                ->orderBy('pd.uploadedAt', 'DESC')
                ->setParameter('manager', $currentModerator)
                ->setMaxResults(self::MAX_RESULTS)
                ->getQuery()
                ->getResult();

            $resultsCount = count($unprocessedCompanyDocuments);
            if ($resultsCount < self::MAX_RESULTS) {
                $maxResults = self::MAX_RESULTS - $resultsCount;
                $unprocessedCompanyDocumentsWithoutManager = $this->em->getRepository('MetalCompaniesBundle:PaymentDetails')
                    ->createQueryBuilder('pd')
                    ->select('pd')
                    ->where('(pd.uploadedAt > pd.approvedAt OR pd.approvedAt IS NULL)')
                    ->andWhere('pd.uploadedAt IS NOT NULL')
                    ->join('pd.company', 'c')
                    ->andWhere('c.manager IS NULL')
                    ->orderBy('pd.uploadedAt', 'DESC')
                    ->setMaxResults($maxResults)
                    ->getQuery()
                    ->getResult();

                $unprocessedCompanyDocuments = array_merge($unprocessedCompanyDocuments, $unprocessedCompanyDocumentsWithoutManager);
            }
        }

        return $this->renderResponse(
            '@MetalCompanies/AdminCompany/Block/unprocessed_company_documents_block_service.html.twig',
            array(
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'unprocessedCompanyDocuments' => $unprocessedCompanyDocuments,
                'isGranted' => $isGranted
            ),
            $response
        );
    }
}
