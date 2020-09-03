<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\MenuItem;
use Metal\CategoriesBundle\Entity\MenuItemClosure;

use Metal\ProjectBundle\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateDataToStroyCommand extends ContainerAwareCommand
{
    /**
     * @var Connection
     */
    private $metalloprokatConn;

    /**
     * @var EntityManagerInterface
     */
    private $metalloprokatEm;

    /**
     * @var Connection
     */
    private $conn;

    /**
     * @var OutputInterface
     */
    private $output;

    protected function configure()
    {
        $this->setName('metal:project:migrate-to-stroy');
        $this->addOption('no-files', null, InputOption::VALUE_NONE);
        $this->addOption('no-metal-update', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;
        $this->output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);

        $this->conn = $em->getConnection();
        /* @var $conn Connection */
        $this->conn->getConfiguration()->setSQLLogger(null);

        $parametersMetalloprokat = $this->getContainer()->getParameter('database_metalloprokat');
        $this->metalloprokatConn = $this->getContainer()->get('doctrine.dbal.connection_factory')->createConnection($parametersMetalloprokat);
        $this->metalloprokatEm = EntityManager::create($this->metalloprokatConn, $em->getConfiguration());

        $menuItemsIdsToTransfer = array(35, 213, 739, 1033, 1034, 1035, 2, 78, 88, 100, 264, 273, 283, 294, 304, 314, 320, 328, 332, 336, 342, 349);

        $menuItemsIds = $this->metalloprokatEm->createQueryBuilder()
            ->select('IDENTITY(mic.descendant) AS id')
            ->from('MetalCategoriesBundle:MenuItemClosure', 'mic')
            ->where('mic.ancestor IN (:menu_items)')
            ->setParameter('menu_items', $menuItemsIdsToTransfer)
            ->orderBy('mic.depth', 'DESC')
            ->getQuery()
            ->getResult();
        // домерживаем 2 главные категории разделов "стройматериалы" и "оборудование и инстумент"
        $menuItemsIds = array_merge(array_column($menuItemsIds, 'id'), array(1071 , 1072));

        $categoriesIds = $menuItems = $this->metalloprokatEm->createQueryBuilder()
            ->select('IDENTITY(mi.category) AS id')
            ->from('MetalCategoriesBundle:MenuItem', 'mi')
            ->where('mi.id IN (:menu_items)')
            ->setParameter('menu_items', $menuItemsIds)
            ->orderBy('id', 'DESC')
            ->getQuery()
            ->getResult();
        $categoriesIds = array_column($categoriesIds, 'id');

        $companiesIds = $this->metalloprokatEm->createQueryBuilder()
            ->select('IDENTITY(cc.company) AS id')
            ->from('MetalCompaniesBundle:CompanyCategory', 'cc')
            ->andWhere('cc.category IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->groupBy('id')
            ->getQuery()
            ->getResult();
        $companiesIds = array_column($companiesIds, 'id');

        $usersIds = $this->metalloprokatEm->createQueryBuilder()
            ->select('u.id')
            ->from('MetalUsersBundle:User', 'u')
            ->andWhere('u.company IN (:companies_ids)')
            ->setParameter('companies_ids', $companiesIds)
            ->getQuery()
            ->getResult();
        $usersIds = array_column($usersIds, 'id');

        $productsIds = $this->metalloprokatEm->createQueryBuilder()
            ->select('p.id')
            ->from('MetalProductsBundle:Product', 'p')
            ->andWhere('p.category IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->getQuery()
            ->getResult();
        $productsIds = array_column($productsIds, 'id');

        $demandsIds = $this->metalloprokatEm->createQueryBuilder()
            ->select('d.id')
            ->from('MetalDemandsBundle:Demand', 'd')
            ->join('d.demandItems', 'di')
            ->andWhere('di.category IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->groupBy('d.id')
            ->getQuery()
            ->getResult();
        $demandsIds = array_column($demandsIds, 'id');

        // metal:project:reset-project со всеми параметрами
        // metal:territorial:update-countries

        // $conn->executeQuery('TRUNCATE mini_site_cover'); файлы еще+юзер файлы+файлы с комапни лого+компани имедж файлы
        // $conn->executeQuery('TRUNCATE Message106'); // Package ?
        // $conn->executeQuery('TRUNCATE company_history'); $company  $relatedCompany
        // $conn->executeQuery('TRUNCATE complaint'); нужно подумать как
        // $conn->executeQuery('TRUNCATE callback'); нужно подумать как
        // $conn->executeQuery('TRUNCATE favorite'); нужно подумать как
        // пересчитать статистику и url_rewrite
        // $conn->executeQuery('TRUNCATE service_packages');  ? завязка есть в Payments

        // metal:project:compile-url-rewrites --truncate
        // metal:demands:fix-collisions пересчет category_id in demand, demand_category
        // metal:companies:update-last-visit пересчет client_ip
        // metal:companies:synchronize-counters пересчет category_attribute_counter
        // metal:categories:generate-category-friends пересчет Category_friends

        // stats_element by product, проверить у звявок категории, удалить на прокате demand_items, product

        $tablesToTransfer = array(
            // Category
            'Message73' => array('Message_ID' => $categoriesIds),
            'category_extended' => array('category_id' => $categoriesIds),
            'category_closure' => array('ancestor' => $categoriesIds),
            'menu_item' => array('id' => $menuItemsIds),
            'menu_item_closure' => array('ancestor' => $menuItemsIds),
            'stats_element' => array('category_id' => $categoriesIds),
            // Company
            'Message75' => array('Message_ID' => $companiesIds),
            'company_delivery_city' => array('company_id' => $companiesIds),
            'company_counter' => array('company_id' => $companiesIds),
            'company_payment_details' => array('company_id' => $companiesIds),
            'Payments' => array('Company_ID' => $companiesIds),
            'company_review_answer' => array('user_id' => $usersIds),
            'mini_site_cover' => array('company_id' => $companiesIds),
            // CompanyCategory
            'Message76' => array('cat_id' => $categoriesIds),
            'company_log' => array('company_id' => $companiesIds),
            'company_description' => array('company_id' => $companiesIds),
            'company_attribute' => array('company_id' => $companiesIds),
            'company_review' => array('company_id' => $companiesIds), //?
            'company_minisite' => array('company_id' => $companiesIds),
            'company_phone' => array('company_id' => $companiesIds),
            // ProductImage
            'Companies_images' => function(QueryBuilder $qb) use ($categoriesIds) {
                $qb
                    ->leftJoin('t', 'Message142', 'p', 'p.Image_ID = t.ID')
                    ->andWhere('(p.Category_ID IN (:categories_ids)) OR (t.Company_ID IS NULL AND t.category_id IN (:categories_ids))')
                    ->setParameter('categories_ids', $categoriesIds, Connection::PARAM_INT_ARRAY)
                    ->groupBy('t.ID');
            },
            'User' => array('ConnectCompany' => $usersIds),
            'user_counter' => array('user_id' => $usersIds),
            'user_visiting' => array('user_id' => $usersIds),
            // Subscriber
            'UserSend' => array('user_id' => $usersIds),
            // Message157 - ParameterGroup
            'Message157' => array('Parent_Razd' => $categoriesIds),
            // Message162 - Parameter
            'Message162' => function(QueryBuilder $qb) use ($categoriesIds) {
                $qb
                    ->join('t', 'Message157', 'pg', 'pg.Message_ID = t.Parent_Razd')
                    ->andWhere('pg.Parent_Razd IN (:categories_ids)')
                    ->setParameter('categories_ids', $categoriesIds, Connection::PARAM_INT_ARRAY);
            },
            // Message155 - ParameterOption
            'Message155' => function(QueryBuilder $qb) use ($categoriesIds) {
                $qb
                    ->join('t', 'Message162', 'p', 'p.Par_ID = t.Message_ID')
                    ->join('p', 'Message157', 'pg', 'pg.Message_ID = p.Parent_Razd')
                    ->andWhere('pg.Parent_Razd IN (:categories_ids)')
                    ->setParameter('categories_ids', $categoriesIds, Connection::PARAM_INT_ARRAY)
                    ->groupBy('t.Message_ID');
            },
            // parameters_types_priorities
            'parameters_types_priorities' => function(QueryBuilder $qb) use ($categoriesIds) {
                $qb
                    ->join('t', 'Message155', 'po', 'po.Type = t.id')
                    ->join('po', 'Message162', 'p', 'p.Par_ID = po.Message_ID')
                    ->join('p', 'Message157', 'pg', 'pg.Message_ID = p.Parent_Razd')
                    ->andWhere('pg.Parent_Razd IN (:categories_ids)')
//                array('Company_ID' => $companiesIds),
                    ->setParameter('categories_ids', $categoriesIds, Connection::PARAM_INT_ARRAY)
                    ->groupBy('t.id');
            },
            // Product
            'Message142' => array('Message_ID' => $productsIds),
            'product_description' => array('product_id' => $productsIds),
            'product_log' => array('product_id' => $productsIds),
            // ProductParameterValue - Message159
            'Message159' => array('Price_ID' => $productsIds),
            'demand' => array('id' => $demandsIds),
            'demand_answer' => array('demand_id' => $demandsIds),
            'demand_category' => array('demand_id' => $demandsIds, 'category_id' => $categoriesIds),
            'demand_item' => array('demand_id' => $demandsIds, 'category_id' => $categoriesIds),
            'demand_subscription_category' => array('category_id' => $categoriesIds),
            'demand_subscription_territorial' => array('user_id' => $usersIds),
            'demand_view' => array('demand_id' => $demandsIds),
        );

        $this->conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0');

        foreach ($tablesToTransfer as $table => $criteria) {
            $this->transferDataFromTable($table, $criteria);
        }

        if (!$input->getOption('no-files')) {
            $this->moveCompanyLogo($companiesIds);
            $this->moveProductPhoto($companiesIds);
            $this->moveDemandsFiles($demandsIds);
            $this->moveUserPhoto($usersIds);
        }

        //TODO: user photo, attach file to demand

        $this->conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        $this->output->writeln(sprintf('Add to Category redirectSlug'));

        $categories = $em->createQueryBuilder()
            ->select('c')
            ->from('MetalCategoriesBundle:Category', 'c', 'c.id')
            ->getQuery()
            ->getResult();
        /* @var $categories Category[] */

        $slugRules = array(
            'krovlia-stroy' => 'vodostochnie-systemi-krovli',
        );
        foreach ($categories as $category) {
            $slug = $category->getSlug();
            if (!empty($slugRules[$slug])) {
                $redirectSlug = $slugRules[$slug];
            } else {
                $redirectSlug = preg_replace('#^stroy[-_]{1}?|[-_]{1}?stroy$#ui', '', $slug);
            }

            $category->setSlug($redirectSlug);

            $em->flush();
        }

        $em->getRepository('MetalCategoriesBundle:Category')->refreshDenormalizedData(
            function ($categoryId, $maxCategoryId) use ($output) {
                $output->writeln(
                    sprintf('Processing denormalized data for categories: %d/%d', $categoryId, $maxCategoryId)
                );
            }
        );

        // Update menu items slug_combined
        $this->output->writeln(sprintf('Update MenuItems slug_combined by category'));
        $this->conn->executeUpdate('
            UPDATE menu_item AS mi
            JOIN Message73 AS c
            ON mi.category_id = c.Message_ID
            SET mi.slug_combined = c.slug_combined
        ');

        if (!$input->getOption('no-metal-update')) {
            $metalCategories = $this->metalloprokatEm->createQueryBuilder()
                ->select('c')
                ->from('MetalCategoriesBundle:Category', 'c', 'c.id')
                ->andWhere('c.id IN (:categories_ids)')
                ->setParameter('categories_ids', array_keys($categories))
                ->getQuery()
                ->getResult();
            /* @var $metalCategories Category[] */

            foreach ($categories as $category) {
                $metalCategories[$category->getId()]->setRedirectSlug($category->getSlugCombined());
                $this->metalloprokatEm->flush();
            }
        }

        $this->output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function transferDataFromTable($table, $criteria)
    {
        $this->output->writeln(sprintf('%s: <info>%s</info>', date('d.m.Y H:i:s'), $table));

        $qb = $this->metalloprokatConn->createQueryBuilder()
            ->select('t.*')
            ->from($table, 't');

        if (is_callable($criteria)) {
            call_user_func($criteria, $qb);
        } else {
            foreach ($criteria as $field => $value) {
                $qb->andWhere(sprintf('t.%s IN (%s)', $field, $qb->createNamedParameter($value, Connection::PARAM_INT_ARRAY)));
            }
        }

        $rows = $qb->execute()->fetchAll();

        foreach ($rows as $row) {
            $this->output->writeln(sprintf('%s: <info>%s:%s</info>', date('d.m.Y H:i:s'), $table, reset($row)));

            $this->conn->insert($table, $row);
        }
    }

    private function moveCompanyLogo($companiesIds)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */

        $originalDir = $this->getContainer()->getParameter('company_original_logos_dir');
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $companiesWithLogoDate = $em->createQueryBuilder()
            ->from('MetalCompaniesBundle:Company', 'c', 'c.id')
            ->select('c.id')
            ->addSelect('c.logo, c.logoMime')
            ->where('c.id IN (:companies_ids)')
            ->setParameter('companies_ids', $companiesIds)
            ->getQuery()
            ->getResult();

        $newDir = $uploadDir.'/company_logo/';
        if (!is_dir($newDir)) {
            mkdir($newDir);
        }
        $logsPath = $this->getContainer()->getParameter('kernel.logs_dir').'/not_moved_logos.txt';

        $withoutLogos = array();
        $notSupported = array();
        $absentLogos = array();
        foreach ($companiesWithLogoDate as $companyId => $logoDate) {
            if ($logoDate['logo']) {
                $withoutLogos[] = $companyId;
            }
            $extension = $imageHelper->getExtensionByMimeType($logoDate['logoMime']);
            if (!$extension) {
                $notSupported[] = array($companyId, $logoDate['logoMime']);
                continue;
            }

            $originalFile = $originalDir . '/' . $logoDate['logo'] . '.' . $extension;
            if (file_exists($originalFile)) {
                $this->output->writeln(sprintf('company - %s: move logo ', $companyId));

                copy(
                    $originalFile,
                    $newDir.'/'.$logoDate['logo'].'.'.$extension
                );
            } else {
                $absentLogos[] = $companyId;
            }
        }

        $date = date('d.m.Y H:i:s');

        file_put_contents($logsPath, print_r(compact('date', 'withoutLogos', 'notSupported', 'absentLogos'), true), FILE_APPEND | LOCK_EX);
        if ($withoutLogos || $notSupported || $absentLogos) {
            $this->output->writeln(sprintf('info about not moved logos see in %s', $logsPath));
        }
    }

    private function moveProductPhoto($companiesIds)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */

        $originalDir = $this->getContainer()->getParameter('original_product_photos');
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $companiesWithImagesDate = $em->createQueryBuilder()
            ->from('MetalProductsBundle:ProductImage', 'pi')
            ->select('pi.id, pi.companyId')
            ->addSelect('pi.name, pi.mimeType')
            ->where('pi.companyId IN (:companies_ids)')
            ->setParameter('companies_ids', $companiesIds)
            ->getQuery()
            ->getResult();

        $newDir = $uploadDir.'/product_photos/';
        if (!is_dir($newDir)) {
            mkdir($newDir);
        }
        $logsPath = $this->getContainer()->getParameter('kernel.logs_dir').'/not_moved_company_images.txt';

        $notSupported = array();
        $absentImages = array();
        foreach ($companiesWithImagesDate as $imageDate) {

            $dirFrom = $originalDir.'/'.$imageDate['companyId'].'-comp';
            $dirTo = $newDir.$imageDate['companyId'].'-comp';

            $extension = $imageHelper->getExtensionByMimeType($imageDate['mimeType']);
            if (!$extension) {
                $notSupported[] = array($imageDate['companyId'], $imageDate['mimeType']);
                continue;
            }

            $originalFile = $dirFrom.'/'.$imageDate['name'].'.'.$extension;
            if (file_exists($originalFile)) {
                $this->output->writeln(sprintf('company %s: move image %s',$imageDate['companyId'], $imageDate['id']));

                if (!is_dir($dirTo)) {
                    mkdir($dirTo);
                }

                copy(
                    $originalFile,
                    $dirTo.'/'.$imageDate['name'].'.'.$extension
                );
            } else {
                $absentImages[] = $imageDate['id'];
            }
        }

        $date = date('d.m.Y H:i:s');

        file_put_contents($logsPath, print_r(compact('date', 'notSupported', 'absentImages'), true), FILE_APPEND | LOCK_EX);
        if ($notSupported || $absentImages) {
            $this->output->writeln(sprintf('info about not moved images see in %s', $logsPath));
        }
    }

    private function moveUserPhoto($usersIds)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */

        $originalDir = $this->getContainer()->getParameter('original_user_avatar_dir');
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $usersWithAvatarDate = $em->createQueryBuilder()
            ->from('MetalUsersBundle:UserAvatar', 'ua')
            ->select('ua.name, ua.mime')
            ->join('ua.user', 'u')
            ->addSelect('u.id AS userId')
            ->where('u.id IN (:users_ids)')
            ->setParameter('users_ids', $usersIds)
            ->getQuery()
            ->getResult();

        $newDir = $uploadDir.'/user_avatar/';
        if (!is_dir($newDir)) {
            mkdir($newDir);
        }
        $avatarsPath = $this->getContainer()->getParameter('kernel.logs_dir').'/not_moved_user_avatars.txt';

        $notSupported = array();
        $absentAvatars = array();
        foreach ($usersWithAvatarDate as $avatarDate) {
            $extension = $imageHelper->getExtensionByMimeType($avatarDate['mime']);
            if (!$extension) {
                $notSupported[] = array($avatarDate['userId'], $avatarDate['mime']);
                continue;
            }

            $originalFile = $originalDir . '/' . $avatarDate['name'] . '.' . $extension;
            if (file_exists($originalFile)) {
                $this->output->writeln(sprintf('user - %s: move avatar ', $avatarDate['userId']));

                copy(
                    $originalFile,
                    $newDir.'/'.$avatarDate['name'].'.'.$extension
                );
            } else {
                $absentAvatars[] = $avatarDate['userId'];
            }
        }

        $date = date('d.m.Y H:i:s');

        file_put_contents($avatarsPath, print_r(compact('date', 'notSupported', 'absentAvatars'), true), FILE_APPEND | LOCK_EX);
        if ($notSupported || $absentAvatars) {
            $this->output->writeln(sprintf('info about not moved avatars see in %s', $avatarsPath));
        }
    }

    private function moveDemandsFiles($demandsIds)
    {
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */

        $originalDir = $this->getContainer()->getParameter('original_demand_files_dir');
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $demandsFilesDate = $em->createQueryBuilder()
            ->from('MetalDemandsBundle:Demand', 'd')
            ->select('d.id')
            ->addSelect('d.filePath, d.fileMime, d.fileOriginalName')
            ->where('d.id IN (:demands_ids)')
            ->andWhere('d.filePath IS NOT NULL')
            ->setParameter('demands_ids', $demandsIds)
            ->getQuery()
            ->getResult();

        $newDir = $uploadDir.'/demands_files/';
        if (!is_dir($newDir)) {
            mkdir($newDir);
        }
        $logsPath = $this->getContainer()->getParameter('kernel.logs_dir').'/not_moved_demand_files.txt';

        $absentImages = array();
        $notSupported = array();
        foreach ($demandsFilesDate as $demandDate) {
            $fileDate = pathinfo($demandDate['filePath']);
            $extension = $fileDate['extension'];
            if (!$extension) {
                $notSupported[] = array($demandDate['id'], $demandDate['filePath']);
                continue;
            }
            $dirName = $fileDate['dirname'];
            $fileName = $fileDate['filename'];

            $dirFrom = $originalDir.'/'.$dirName;
            $dirTo = $newDir.$dirName;

            $originalFile = $dirFrom.'/'.$fileName.'.'.$extension;

            if (file_exists($originalFile)) {
                $this->output->writeln(sprintf('demand %s: move file %s',$demandDate['id'], $demandDate['fileOriginalName']));

                if (!is_dir($dirTo)) {
                    mkdir($dirTo, 0777, true);
                }

                copy(
                    $originalFile,
                    $dirTo.'/'.$fileName.'.'.$extension
                );
            } else {
                $absentImages[] = $demandDate['id'];
            }
        }

        $date = date('d.m.Y H:i:s');

        file_put_contents($logsPath, print_r(compact('date', 'notSupported', 'absentImages'), true), FILE_APPEND | LOCK_EX);
        if ($notSupported || $absentImages) {
            $this->output->writeln(sprintf('info about not moved demands files see in %s', $logsPath));
        }
    }
}
