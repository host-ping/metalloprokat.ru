<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\DemandsBundle\DataFetching\Spec\DemandFilteringSpec;
use Metal\DemandsBundle\DataFetching\Spec\DemandOrderingSpec;
use Metal\UsersBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExportDemandCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;

    protected function configure()
    {
        $this->setName('metal:demands:export-demands');
        $this->addArgument('user-id', InputArgument::REQUIRED, 'ID user with which to select demands.');
        $this->addOption('format', null, InputOption::VALUE_OPTIONAL, 'Allowed formats: xlsx|csv', 'xlsx');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command %s', date('Y-m-d H:i'), $this->getName()));

        $container = $this->getContainer();

        $this->em = $container->get('doctrine.orm.default_entity_manager');

        $allowedFormats = array('xlsx', 'csv');
        $format = $input->getOption('format');
        if (!in_array($format, $allowedFormats, true)) {
            throw new \InvalidArgumentException(sprintf('Формат не поддерживается, доступные форматы: "%s".', implode(', ', $allowedFormats)));
        }

        $user = $this->em->find('MetalUsersBundle:User', $input->getArgument('user-id'));

        if (!$user instanceof User) {
            throw new \InvalidArgumentException('Пользователь не найден.');
        }

        if (!$user->getCompany()) {
            throw new \InvalidArgumentException('Пользователь не привязан к компании.');
        }

        $demandExportService = $container->get('metal.demands.demand_export_service');
        $demandsDataFetcher = $container->get('metal.demands.data_fetcher');

        list($criteria, $orderBy) = $this->prepareCriteria($user);
        //!NB Если не передавать perPage будет валится с Pagerfanta\Exception\NotIntegerMaxPerPageException
        $resultSet = $demandsDataFetcher->getResultSetByCriteria($criteria, $orderBy, 100000);
        $demandsIds = array_column(iterator_to_array($resultSet), 'id');

        $fileName = $demandExportService->getExportFileName(
            $demandsIds,
            $user->getCompany(),
            $format,
            'subscription_demands',
            $user,
            $criteria,
            $orderBy
        );

        $locationFile = $container->getParameter('upload_dir').'/demands-export/'.$fileName;

        $output->writeln(sprintf('%s: File location "%s"', date('Y-m-d H:i'), realpath($locationFile)));
    }

    private function prepareCriteria(User $user)
    {
        $criteria = new DemandFilteringSpec();
        $criteria->country($user->getCountry());
        $criteria->maxMatches(100000);

        $userId = $user->getId();
        $categoriesIds = $this->em->getRepository('MetalDemandsBundle:DemandSubscriptionCategory')
            ->getCategoryIdsPerUser([$userId])[$userId];

        if ($categoriesIds) {
            $criteria->categoriesIds($categoriesIds);
        }

        $territorialIds = $this->em->getRepository('MetalDemandsBundle:DemandSubscriptionTerritorial')
            ->getSubscribedTerritorialIdsPerUser([$userId])[$userId];

        if ($territorialIds) {
            $criteria->territorialStructureIds($territorialIds);
        }

        $orderBy = new DemandOrderingSpec();
        $orderBy->createdAt();

        return array($criteria, $orderBy);
    }
}
