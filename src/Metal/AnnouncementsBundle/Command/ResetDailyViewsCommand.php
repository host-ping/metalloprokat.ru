<?php

namespace Metal\AnnouncementsBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ResetDailyViewsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:announcements:reset-daily-views-count');
    }

    public function run(InputInterface $input, OutputInterface $output)
    {
        $this->getContainer()->get('doctrine.dbal.default_connection')->executeUpdate('UPDATE announcement SET daily_views_count = 0');
    }
}
