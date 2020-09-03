<?php

namespace Metal\AttributesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RefreshAttributeValuePrioritiesCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:attributes:refresh-attribute-value-priorities');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i:s')));
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        $attributeValueRepository = $em->getRepository('MetalAttributesBundle:AttributeValue');

        $output->writeln(sprintf('%s: Refresh minisite priority', date('Y-m-d H:i:s')));
        $attributeValueRepository->refreshPriorityOrders();

        $output->writeln(sprintf('%s: Refresh url priority', date('Y-m-d H:i:s')));
        $attributeValueRepository->refreshUrlPriorityOrder();

        $output->writeln(sprintf('%s: Refresh attribute category', date('Y-m-d H:i:s')));
        $em->getRepository('MetalAttributesBundle:AttributeCategory')->refreshAttributeCategory();

        $output->writeln(sprintf('%s: End command %s', date('Y-m-d H:i:s'), $this->getName()));
    }
}
