<?php

namespace Metal\TerritorialBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *  @see \Application\Migrations\Version20160404165245
 */
class MergeCitiesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:territorial:merge-cities');
        $this->addArgument('main-id', InputArgument::REQUIRED);
        $this->addArgument('additional-id', InputArgument::REQUIRED);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

//        1588	Свердловск 44
//        1263	Новоуральск

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $conn = $em->getConnection();
        $mainCityId = (int)$input->getArgument('main-id');
        $additionalCityId = (int)$input->getArgument('additional-id');

        $oldTerritorialStructure = $em->getRepository('MetalTerritorialBundle:TerritorialStructure')->findOneBy(array('city' => $additionalCityId));
        $oldTerritorialStructureId = $oldTerritorialStructure->getId();

        $conn->beginTransaction();

        $conn->executeUpdate("UPDATE User SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE demand SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE demand_answer SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("
            UPDATE IGNORE demand_subscription_territorial dst
            SET dst.territorial_structure_id = (SELECT ts.id FROM territorial_structure ts WHERE ts.city_id = :mainId)
            WHERE dst.city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );

        $conn->executeUpdate("UPDATE demand_subscription_territorial SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );

        // обновляем подписки если это возможно
        $conn->executeQuery("
            UPDATE IGNORE demand_subscription_territorial dst
            SET dst.territorial_structure_id = (SELECT ts.id FROM territorial_structure ts WHERE ts.city_id = :mainId)
            WHERE dst.city_id = :mainId",
            array('mainId' => $mainCityId)
        );

        // удаляем после всех действий подписку на несуществующую структуру
        $conn->executeQuery("DELETE FROM demand_subscription_territorial WHERE territorial_structure_id = :structureId",
            array('structureId' => $oldTerritorialStructureId)
        );

        $conn->executeUpdate("UPDATE user_city SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE service_packages SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE announcement_stats_element SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE brand_city SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE manufacturer_city SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE catalog_product_city SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE catalog_product_review SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE company_review SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE complaint SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE IGNORE stats_city SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE stats_element SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE metalspros_demand_subscription SET city_id = :mainId WHERE city_id = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );
        $conn->executeUpdate("UPDATE Message75 SET company_city = :mainId WHERE company_city = :additionalId",
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );

//        $conn->executeUpdate("UPDATE `Classificator_Region` SET `Region_Name`= 'Великий Новгород'  WHERE `Region_ID`= :mainId ");

        $conn->executeUpdate("DELETE FROM territorial_structure WHERE city_id = :additionalId",
            array('additionalId' => $additionalCityId)
        );


        // обновляем филиалы с правильным id
        $conn->executeUpdate('UPDATE IGNORE company_delivery_city SET city_id = :mainId WHERE city_id = :additionalId',
            array('mainId' => $mainCityId, 'additionalId' => $additionalCityId)
        );

        // получаем продукты, у которых остались филиалы с неправильным id . Там где сработал duplicate key
        $products = $conn->fetchAll('SELECT p.Message_ID, p.Company_ID, p.Name, p.Memo FROM Message142 p
            JOIN company_delivery_city cdc ON p.branch_office_id = cdc.id
            WHERE cdc.city_id = :additionalId
            ',
            array('additionalId' => $additionalCityId)
            );

        // ставим таким продуктам филиал с правильным id иначе главный офис компании
        foreach ($products as $product) {
            try {
                $conn->executeUpdate('
                UPDATE Message142 p SET p.branch_office_id =
                (
                    SELECT cdc.id FROM company_delivery_city cdc WHERE cdc.company_id = :companyId AND cdc.city_id = :cityId
                )
                WHERE p.Message_ID = :productId',
                    array('companyId' => $product['Company_ID'], 'cityId' => $mainCityId, 'productId' => $product['Message_ID'])
                );

                $deliveryId = $conn->fetchColumn('SELECT branch_office_id FROM Message142 WHERE Message_ID = :productId', array('productId' => $product['Message_ID']));
                $hash = sha1(serialize(array((int)$deliveryId, (string)$product['Name'], (string)$product['Memo'])));

                $output->writeln(sprintf('Update product %d, delivery %d ', $product['Message_ID'], (int)$deliveryId));

                $conn->executeUpdate('
                UPDATE Message142 p SET p.item_hash = :hash
                WHERE p.Message_ID = :productId',
                    array('productId' => $product['Message_ID'], 'hash' => $hash)
                );
                $output->writeln('Update hash');

            } catch (\Exception $e) {
                $conn->executeUpdate('
                UPDATE Message142 p SET p.branch_office_id =
                (
                    SELECT cdc.id FROM company_delivery_city cdc WHERE cdc.company_id = :companyId AND cdc.is_main_office = 1
                )
                WHERE p.Message_ID = :productId',
                    array('companyId' => $product['Company_ID'], 'productId' => $product['Message_ID'])
                );

                $deliveryId = $conn->fetchColumn('SELECT branch_office_id FROM Message142 WHERE Message_ID = :productId', array('productId' => $product['Message_ID']));
                $hash = sha1(serialize(array((int)$deliveryId, (string)$product['Name'], (string)$product['Memo'])));

                $output->writeln(sprintf('Update product %d, main delivery %d ', $product['Message_ID'], (int)$deliveryId));

                $conn->executeUpdate('
                UPDATE Message142 p SET p.item_hash = :hash
                WHERE p.Message_ID = :productId',
                    array('productId' => $product['Message_ID'], 'hash' => $hash)
                );

                $output->writeln('Update hash');
            }

        }

        // удаляем филиал с устаревшим городом
        $conn->executeQuery('DELETE FROM company_delivery_city WHERE city_id = :additionalId', array( 'additionalId' => $additionalCityId));

        // удаляем из статистики то, что в миграции update ignore пропустил
        $conn->executeQuery('DELETE FROM stats_city WHERE city_id = :additionalId', array( 'additionalId' => $additionalCityId));

        // удаляем после всех действий город из таблицы городов
        $conn->executeQuery("DELETE FROM Classificator_Region WHERE Region_ID = :additionalId", array( 'additionalId' => $additionalCityId));

        // проставляем падежи Новгороду
/*        $conn->executeQuery("
            UPDATE Classificator_Region SET
                title_locative = 'Великом Новгороде',
                title_genitive = 'Великого Новгорода',
                title_accusative = 'Великий Новгород'
            WHERE Region_ID = 1219
        ");
*/

        // переименовываем город в территориальной структуре
//        $conn->executeQuery("UPDATE territorial_structure SET title = 'Великий Новгород' WHERE city_id = :mainId");

        $conn->commit();

        $output->writeln(sprintf('%s: Completed', date('d.m.Y H:i:s')));
    }
}
