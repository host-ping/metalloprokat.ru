<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\CategoriesBundle\Helper\DefaultHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAttributesSlugsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:categories:update-attributes-slugs');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $container = $this->getContainer();

        $conn = $container->get('doctrine')->getConnection();
        /* @var $conn Connection */
        $conn->getConfiguration()->setSQLLogger(null);

        $conn->executeUpdate('UPDATE Message155 SET slug = Keyword');

        $attributes = $conn->fetchAll("SELECT Message_ID, Keyword RLIKE '[,a-zA-Z0-9]+' as rr, Keyword
          FROM Message155 HAVING rr > 0");

        $categoriesHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalCategoriesBundle');
        /* @var $categoriesHelper DefaultHelper */
        foreach ($attributes as $attribute) {
            $slug = $categoriesHelper->normalizeSlug($attribute['Keyword']);

            $conn->executeUpdate('UPDATE Message155 SET slug = :slug WHERE Message_ID = :id',
                array('id' => $attribute['Message_ID'], 'slug' => $slug));
        }

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
