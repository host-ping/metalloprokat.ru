<?php

namespace Metal\CategoriesBundle\Command;

use Metal\CategoriesBundle\Entity\CategoryFriends;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class GenerateCategoryFriendsCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:categories:generate-category-friends');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();

        $limit = 52;

        if ($input->getOption('truncate')) {
            $conn->executeUpdate('TRUNCATE Category_friends');
        }

        $sql = 'SELECT distinct(`descendant`) cat
                FROM
                `category_closure`
                WHERE
                `descendant` NOT IN
                (SELECT Category_ID FROM `Category_friends`)';

        $emptyIds = $conn->fetchAll($sql);
        foreach ($emptyIds as $key => $val) {
            $friends = array();

            $sql = 'SELECT Message_ID
                    FROM Message73
                    WHERE Message_ID
                    IN (
                    SELECT `descendant`
                    FROM `category_closure`
                    WHERE `ancestor` NOT
                    IN (
                    SELECT `ancestor`
                    FROM category_closure
                    WHERE descendant = :cat
                    AND ancestor <> :cat
                    )
                    )
                    ORDER BY RAND()
                    LIMIT '.$limit;

            $friendIds = $conn->fetchAll($sql, array('cat' => $val['cat']));

            foreach ($friendIds as $valFr) {
                $friends[] = $valFr['Message_ID'];
            }

            $friendsFlags = $this->generateRandArray(count($friends));
            $sql = 'INSERT IGNORE INTO `Category_friends` (`Category_ID`,`Value`,`Links`)
                    VALUES
                    (:id, :FrindsIds, :FriendsFlags)';

            $conn->executeQuery($sql, array('FrindsIds' => implode(',', $friends), 'FriendsFlags' => implode(',', $friendsFlags), 'id' => $val['cat']));

            $output->writeln(sprintf('%s : %s updated', $key, $val['cat']));
        }

        $output->writeln('done');
    }

    private function generateRandArray($length)
    {
        $array = array();
        for ($i = 0; $i < $length; $i++) {
            $array[$i] = array_rand(
                array(
                    CategoryFriends::ANCHOR_CATEGORY_NAME => CategoryFriends::ANCHOR_CATEGORY_NAME,
                    CategoryFriends::ANCHOR_CATEGORY_NAME_WITH_REGION => CategoryFriends::ANCHOR_CATEGORY_NAME_WITH_REGION,
                    CategoryFriends::ANCHOR_CATEGORY_NAME_WITH_IN_REGION => CategoryFriends::ANCHOR_CATEGORY_NAME_WITH_IN_REGION,
                )
            );
        }

        return $array;
    }
}
