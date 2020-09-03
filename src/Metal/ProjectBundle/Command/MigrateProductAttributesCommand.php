<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateProductAttributesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:migrate-attributes');
        $this->addOption('step2', null, InputOption::VALUE_NONE);
        $this->addOption('truncate', null, InputOption::VALUE_NONE);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $em->getRepository('MetalProjectBundle:Site')->disableLogging();

        if ($input->getOption('truncate')) {
            $output->writeln(sprintf('%s: <info>Truncate.</info>', date('d.m.Y H:i:s')));
            $conn->executeUpdate('TRUNCATE attribute');
            $conn->executeUpdate('TRUNCATE attribute_value');
            $conn->executeUpdate('TRUNCATE attribute_value_category');
            $conn->executeUpdate('TRUNCATE attribute_value_category_friend');
            $conn->executeUpdate('TRUNCATE attribute_value_category_friend_category');
            $conn->executeUpdate('TRUNCATE product_attribute_value');
        }

        $categoriesIds = array_column($conn->fetchAll('SELECT Message_ID AS id FROM Message73'), 'id', 'id');

        if (!$input->getOption('step2')) {
            $conn->executeQuery('
              INSERT IGNORE INTO attribute
                (id, title, code, url_priority, output_priority, indexable_for_seo)
              VALUES
                (1, "Марка стали", "mark", 2, 1, TRUE),
                (2, "Гост, ТУ", "gost", 3, 2, TRUE),
                (3, "Размер", "size", 0, 3, TRUE),
                (4, "Класс", "class", 1, 0, TRUE),
                (5, "Тип", "type", 4, 4, TRUE),
                (6, "Вид", "vid", 5, 5, TRUE),
                (7, "Состояние", "condition", 6, 6, TRUE),
                (8, "Назначение", "purpose", 7, 7, TRUE),
                (9, "Тип стенки", "sidetype", 8, 8, TRUE),
                (10, "Блеск", "shinetype", 9, 9, TRUE),
                (11, "Материал", "material", 10, 10, TRUE),
                (12, "Тип покрытия", "covertype", 11, 11, TRUE),
                (13, "Изоляция", "izolacia", 12, 12, TRUE)  
                ');

            $conn->executeQuery('
                INSERT IGNORE INTO attribute_value
                    (id, value, attribute_id, slug, regex_match, additional_info)
                SELECT Message_ID, name,Type, slug, sinonym, Steel
                FROM Message155 WHERE TYPE IN (
                SELECT id
                FROM attribute)'
            );

            //parameter-category
            $resGr = $conn->fetchAll('SELECT Message_ID, Parent_Razd, Type FROM Message157');

            foreach ($resGr as $group) {
                $resCategAttrs = $conn->fetchAll('
                  SELECT m162.Message_ID, Par_ID, FriendIDs, FriendsType, FriendCatIDs, FriendsCatType
                  FROM Message162 m162 JOIN attribute_value m155 on m155.id  = m162.Par_ID
                  WHERE Parent_Razd = :razdID',
                    array('razdID' => $group['Message_ID'])
                );

                $output->writeln(sprintf('%s: group %d', date('d.m.Y H:i:s'), $group['Message_ID']));

                foreach ($resCategAttrs as $resCategAttr) {
                    $conn->executeQuery('
                        INSERT IGNORE INTO attribute_value_category
                        (id, attribute_value_id, category_id)
                        VALUES
                        (:oldID, :par, :category)',
                        array('oldID' => $resCategAttr['Message_ID'], 'par' => $resCategAttr['Par_ID'], 'category' => $group['Parent_Razd'])
                    );
                }
            }
        } else {
            //parameter-category

            $attrValuesCatRangeIds = $conn->fetchArray('SELECT MIN(avc.id), MAX(avc.id) FROM attribute_value_category AS avc');

            $minId = $attrValuesCatRangeIds[0];
            $maxId = $attrValuesCatRangeIds[1];

            $idFrom = $minId;

            do {
                $idTo = $idFrom + 500;

                $resAttrValuesCat = $conn->fetchAll(
                    'SELECT avc.id FROM attribute_value_category AS avc WHERE avc.id >= :id_from AND avc.id < :id_to',
                    array(
                        'id_from' => $idFrom,
                        'id_to' => $idTo
                    )
                );

                foreach ($resAttrValuesCat as $attrValue) {

                    $resCategAttrs = $conn->fetchAll('
                          SELECT m162.Message_ID, Par_ID, FriendIDs, FriendsType, FriendCatIDs, FriendsCatType
                          FROM Message162 m162 WHERE Message_ID = :razdID',
                        array('razdID' => $attrValue['id'])
                    );

                    $output->writeln(sprintf('%s: parameter_category %d', date('d.m.Y H:i:s'), $attrValue['id']));

                    foreach ($resCategAttrs as $resCategAttr) {
                        $friendIds = explode(',', $resCategAttr['FriendIDs']);
                        $flags = explode(',', $resCategAttr['FriendsType']);

                        if (count($friendIds) != count($flags)) {
                            $output->writeln(sprintf('%s: Bad data for Message162 razdId = %d ', date('d.m.Y H:i:s'), $attrValue['id']));
                            continue;
                        }

                        $resFrs = array_combine($friendIds, $flags);

                        foreach ($resFrs as $key => $resFr) {

                            $fr = $conn->fetchColumn('
                                SELECT id FROM attribute_value_category WHERE id = :id',
                                array('id' => $key)
                            );

                            if ($fr) {
                                $conn->executeQuery('
                                    INSERT IGNORE INTO attribute_value_category_friend
                                        (attribute_value_category_id, attribute_value_category_friend_id, flag)
                                    VALUES
                                        (:attribute_id, :attribute_friend_id, :flag)',
                                    array('attribute_id' => $resCategAttr['Message_ID'], 'attribute_friend_id' => $key, 'flag' => $resFr));
                            }
                        }

                        //categories - friends
                        $friendIds = explode(',', $resCategAttr['FriendCatIDs']);
                        $flags = explode(',', $resCategAttr['FriendsCatType']);
                        $resFrs = array_combine($friendIds, $flags);
                        foreach ($resFrs as $key => $resFr) {
                            if ($key > 0 && isset($categoriesIds[$key])) {
                                $conn->executeQuery(
                                    'INSERT IGNORE INTO attribute_value_category_friend_category
                                        (attribute_value_category_id, category_id, flag)
                                        VALUES (:attribute_id, :attribute_friend_id, :flag)',
                                    array(
                                        'attribute_id' => $resCategAttr['Message_ID'],
                                        'attribute_friend_id' => $key,
                                        'flag' => $resFr,
                                    )
                                );
                            }
                        }
                    }
                }

                $idFrom = $idTo;

            } while ($idFrom <= $maxId);

            //product-parameter

            $output->writeln(sprintf('%s: Transfer to product_attribute_value', date('d.m.Y H:i:s')));
            $conn->executeQuery('
                INSERT IGNORE INTO product_attribute_value
                (product_id, attribute_value_id)
                SELECT Price_ID, GostM_ID FROM Message159 JOIN attribute_value av ON av.id = GostM_ID
            ');
        }

        $em->getRepository('MetalProjectBundle:Site')->restoreLogging();

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
