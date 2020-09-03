<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ResetProjectCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:reset-project');
        $this->addOption('companies-products-users', null, InputOption::VALUE_NONE);
        $this->addOption('banners', null, InputOption::VALUE_NONE);
        $this->addOption('demands', null, InputOption::VALUE_NONE);
        $this->addOption('statistics', null, InputOption::VALUE_NONE);
        $this->addOption('support-orders', null, InputOption::VALUE_NONE);
        $this->addOption('categories', null, InputOption::VALUE_NONE);
        $this->addOption('content', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $output->writeln(sprintf('%s: TRUNCATE tables', date('d.m.Y H:i:s')));
        $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0');

        if ($input->getOption('companies-products-users')) {
            $conn->executeQuery('TRUNCATE company_counter');
            $conn->executeQuery('TRUNCATE company_description');
            $conn->executeQuery('TRUNCATE company_log');
            $conn->executeQuery('TRUNCATE company_payment_details');
            $conn->executeQuery('TRUNCATE company_delivery_city');
            if ($conn->getSchemaManager()->tablesExist('company_city_title')) {
                $conn->executeQuery('TRUNCATE company_city_title');
            }

            $conn->executeQuery('TRUNCATE company_registration');
            $conn->executeQuery('TRUNCATE company_attribute');
            $conn->executeQuery('TRUNCATE company_old_slug');
            $conn->executeQuery('TRUNCATE mini_site_cover');
            $conn->executeQuery('TRUNCATE Message106'); // Package
            $conn->executeQuery('TRUNCATE company_minisite');
            $conn->executeQuery('TRUNCATE company_phone');
            $conn->executeQuery('TRUNCATE Message76'); // CompanyCategory
            $conn->executeQuery('TRUNCATE Companies_images');
            $conn->executeQuery('TRUNCATE Filetable');
            $conn->executeQuery('TRUNCATE company_review');
            $conn->executeQuery('TRUNCATE company_review_answer');
            $conn->executeQuery('TRUNCATE company_history');
            $conn->executeQuery('TRUNCATE complaint');
            $conn->executeQuery('TRUNCATE callback');
            $conn->executeQuery('TRUNCATE Message75'); // Company
            $conn->executeQuery('TRUNCATE url_rewrite');

            $conn->executeQuery('TRUNCATE User');
            $conn->executeQuery('TRUNCATE user_counter');
            $conn->executeQuery('TRUNCATE UserSend');
            $conn->executeQuery('TRUNCATE newsletter');
            $conn->executeQuery('TRUNCATE newsletter_recipient');
            $conn->executeQuery('TRUNCATE client_ip');
            $conn->executeQuery('TRUNCATE site');
            $conn->executeQuery('TRUNCATE favorite');
            $conn->executeQuery('TRUNCATE favorite_company');
            $conn->executeQuery('TRUNCATE metalspros_demand_subscription');
            $conn->executeQuery('TRUNCATE demand_subscription_category');
            $conn->executeQuery('TRUNCATE demand_subscription_territorial');
            $conn->executeQuery('TRUNCATE user_visiting');

            $conn->executeQuery('TRUNCATE Message177'); // Client
            $conn->executeQuery('TRUNCATE Message159'); // ProductParameterValue
            $conn->executeQuery('TRUNCATE Message162'); // Parameter
            $conn->executeQuery('TRUNCATE Message155'); // ParameterOption
            $conn->executeQuery('TRUNCATE Message157'); // ParameterGroup
            $conn->executeQuery('TRUNCATE parameters_types_priorities'); // ParameterTypes
            $conn->executeQuery('TRUNCATE Message142'); // Product
            $conn->executeQuery('TRUNCATE product_description');
            $conn->executeQuery('TRUNCATE product_log');
            $conn->executeQuery('TRUNCATE Payments');
        }

        if ($input->getOption('banners')) {
            $conn->executeQuery('TRUNCATE announcement');
            $conn->executeQuery('TRUNCATE announcement_order');
            if ($conn->getSchemaManager()->tablesExist('announcement_stats_element')) {
                $conn->executeQuery('TRUNCATE announcement_stats_element');
            }
            $conn->executeQuery('TRUNCATE announcement_zone_status');
        }

        if ($input->getOption('demands')) {
            $conn->executeQuery('TRUNCATE demand');
            $conn->executeQuery('TRUNCATE demand_item');
            $conn->executeQuery('TRUNCATE demand_category');
            $conn->executeQuery('TRUNCATE demand_answer');
            $conn->executeQuery('TRUNCATE demand_view');

            $conn->executeQuery('TRUNCATE grabber_log');
            $conn->executeQuery('TRUNCATE grabber_parsed_demand');
            $conn->executeQuery('TRUNCATE grabber_site');
        }

        if ($input->getOption('statistics')) {
            $conn->executeQuery('TRUNCATE stats_city');
            $conn->executeQuery('TRUNCATE stats_category');
            $conn->executeQuery('TRUNCATE stats_daily');
            if ($conn->getSchemaManager()->tablesExist('stats_element')) {
                $conn->executeQuery('TRUNCATE stats_element');
            }
            if ($conn->getSchemaManager()->tablesExist('stats_product_change')) {
                $conn->executeQuery('TRUNCATE stats_product_change');
            }
        }

        if ($input->getOption('support-orders')) {
            $conn->executeQuery('TRUNCATE support_answer');
            $conn->executeQuery('TRUNCATE support_topic');
            $conn->executeQuery('TRUNCATE announcement_order');
            $conn->executeQuery('TRUNCATE service_packages');
        }

        if ($input->getOption('categories')) {
            $conn->executeQuery('TRUNCATE Message73');
            $conn->executeQuery('TRUNCATE menu_item');
            $conn->executeQuery('TRUNCATE category_closure');
            $conn->executeQuery('TRUNCATE menu_item_closure');
            $conn->executeQuery('TRUNCATE category_attribute_counter');
            $conn->executeQuery('TRUNCATE category_extended');
            $conn->executeQuery('TRUNCATE Category_friends');
            $conn->executeQuery('TRUNCATE category_test_item');
            $conn->executeQuery('TRUNCATE redirect');
            $conn->executeQuery('TRUNCATE landing_template');
        }

        if ($input->getOption('content')) {
            $conn->executeQuery('TRUNCATE content_tag');
            $conn->executeQuery('TRUNCATE content_user_category');
            $conn->executeQuery('TRUNCATE content_entry');
            $conn->executeQuery('TRUNCATE content_entry_tag');
            $conn->executeQuery('TRUNCATE content_comment');
            $conn->executeQuery('TRUNCATE content_category');
            $conn->executeQuery('TRUNCATE content_category_closure');
            $conn->executeQuery('TRUNCATE content_image');
            $conn->executeQuery('TRUNCATE content_image_album');
        }

        $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1');
        $output->writeln(sprintf('%s: Command done.', date('d.m.Y')));
    }
}
