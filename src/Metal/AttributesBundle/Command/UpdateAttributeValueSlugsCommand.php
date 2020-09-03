<?php

namespace Metal\AttributesBundle\Command;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateAttributeValueSlugsCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('metal:attributes:update-slugs')
            ->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i:s')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();

        if ($input->getOption('truncate')) {
            $output->writeln('Truncate AttributeValue');
            $conn->executeUpdate('UPDATE attribute_value SET slug = NULL');
        }

        $attributeValues = $em->createQueryBuilder()
            ->from('MetalAttributesBundle:AttributeValue', 'av', 'av.id')
            ->select('av.id, av.value', 'a.code')
            ->join('av.attribute', 'a')
            ->where('av.slug IS NULL')
            ->getQuery()
            ->getResult();

        $slugify = $this->getContainer()->get('slugify');
        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');

        foreach ($attributeValues as $key => $attributeValue) {
            $slug = $attributeValueRepository->generateUniqueSlug(
                $attributeValue['value'],
                $attributeValue['code'],
                $slugify
            );
            $conn->executeUpdate(
                'UPDATE attribute_value SET slug = :slug WHERE id = :id',
                array('slug' => $slug, 'id' => $key)
            );
        }

        $output->writeln(sprintf('%s: End command %s', date('Y-m-d H:i:s'), $this->getName()));
    }
}
