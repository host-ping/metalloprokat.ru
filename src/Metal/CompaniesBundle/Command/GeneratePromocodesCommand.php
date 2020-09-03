<?php

namespace Metal\CompaniesBundle\Command;

use Metal\CompaniesBundle\Entity\Promocode;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GeneratePromocodesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('metal:companies:generate-promocodes')
            ->addOption('starts-at', null, InputOption::VALUE_REQUIRED, 'Format 10.11.2015')
            ->addOption('ends-at', null, InputOption::VALUE_REQUIRED, 'Format 20.11.2015')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, '', 60);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine')->getManager();
        $startsAt = new \DateTime($input->getOption('starts-at'));
        $endsAt = new \DateTime($input->getOption('ends-at'));
        $limit = $input->getOption('limit');
        $companyHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalCompaniesBundle');
        /* @var $companyHelper DefaultHelper */

        for ($i = 0; $i < $limit; $i++) {
            $promocode = new Promocode();
            $promocode->setCode($companyHelper->generatePromocode());
            $promocode->setStartsAt($startsAt);
            $promocode->setEndsAt($endsAt);
            $em->persist($promocode);
            $em->flush();

            $output->writeln(sprintf('%s: Generate code: "%s"', date('d.m.Y H:i:s'), $promocode->getCode()));
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
