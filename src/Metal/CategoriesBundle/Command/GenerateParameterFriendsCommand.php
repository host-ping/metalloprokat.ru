<?php

namespace Metal\CategoriesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManager;

class GenerateParameterFriendsCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this->setName('metal:categories:generate-parameter-friends');
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('%s: Truncate Message162', date('d.m.Y H:i:s')));
            $conn->executeUpdate('UPDATE Message162 SET FriendIDs = ""');
        }

        $output->writeln(sprintf('%s: Get Message162 where empty FriendIDs', date('d.m.Y H:i:s')));
        $sql = 'SELECT m162.`Message_ID` , m162.`Par_ID` , m157.Type, m157.Parent_Razd
                    FROM `Message162` m162
                    JOIN Message157 m157 ON m162.`Parent_Razd` = m157.`Message_ID`
                    WHERE `FriendIDs` = ""';
        $emptyIds = $conn->fetchAll($sql);
        foreach ($emptyIds as $key => $val) {
            $friends = array();
            $friendsFlags = $this->generateRandArray(33);
            $sql = "SELECT `Message_ID` , `Parent_Razd`
                    FROM `Message162`
                    WHERE `Par_ID` = :Parameter
                      AND Message_ID <> :id
                    ORDER BY RAND() limit 17";
            $friendIds = $conn->fetchAll($sql, array('Parameter' => $val['Par_ID'], 'id' => $val['Message_ID']));
            foreach ($friendIds as $valFr) {
                $friends[] = $valFr['Message_ID'];
            }

            $sql = 'SELECT m162.Message_ID
                        FROM `Message162` m162
                        JOIN Message157 m157 ON m157.Message_ID = m162.Parent_Razd
                        WHERE TYPE = :type
                          AND m157.Parent_Razd <> :Parent_Razd
                          AND m162.Message_ID <> :id
                        ORDER BY RAND() limit 16';
            $friendIds = $conn->fetchAll($sql, array('type' => $val['Type'], 'id' => $val['Message_ID'], 'Parent_Razd' => $val['Parent_Razd']));
            foreach ($friendIds as $valFr) {
                $friends[] = $valFr['Message_ID'];
            }

            $friendsIdsAsString = implode(',', $friends);
            $friendsFlagsAsString = implode(',', $friendsFlags);

            $output->writeln(sprintf('%s: UPDATE Message162 FriendIDs = %s FriendsType = %s for id = %s', date('d.m.Y H:i:s'), $friendsIdsAsString, $friendsFlagsAsString, $val['Message_ID']));

            $sql = 'UPDATE `Message162` SET `FriendIDs` = :FrindsIds, `FriendsType` = :FriendsFlags
                WHERE `Message_ID` = :id';
            $conn->executeUpdate($sql, array('FrindsIds' => $friendsIdsAsString, 'FriendsFlags' => $friendsFlagsAsString, 'id' => $val['Message_ID']));
        }

        $output->writeln('done');
    }

    private function generateRandArray($length)
    {
        $arr = array();
        for ($i = 0; $i < $length; $i++) {
            $arr[$i] = mt_rand(0, 3);
        }
        return $arr;
    }
}
