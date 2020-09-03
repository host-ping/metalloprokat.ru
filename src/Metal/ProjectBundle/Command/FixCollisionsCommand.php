<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixCollisionsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:fix-collisions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $magicDate ='2011-01-01';
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        //Компании с Created = 0 OR Created is null OR c.Created > u.Created обновляем их дату создания до даты создания пользователя (ConnectCompany)
        //проблема продуктов - LastUpdated тоже обновится
        $conn->executeUpdate(
            '
            UPDATE Message75 c
              LEFT JOIN User u ON u.ConnectCompany = c.Message_ID
            SET c.Created = (
              SELECT min(u.Created) FROM User u WHERE u.ConnectCompany = c.Message_ID AND u.Created > :created ),
               c.ReplicationStatus = IF (inCrm = 1, 1, 0)
            WHERE
              (c.Created > u.Created OR c.Created = :created)
              AND
              u.Created > :created
            ;',
            array(
                'created' => '0000-00-00'
            )
        );

        //?? Для оставшихся компаний с Created = 0 проставляем дату '2011-01-01' - эта дата уже похоже когда-то использовалась для компаний с 0
        $conn->executeUpdate(
            '
            UPDATE Message75
            SET Created = :newDate, ReplicationStatus = 1
            WHERE
               Created = :created
            ;',
            array(
                'newDate' => $magicDate,
                'created' => '0000-00-00'
            )
        );

        //Пользователей с Created = 0 OR Created is null обновляем до даты создания компании (ConnectCompany)
        $conn->executeUpdate(
            '
            UPDATE User u
              JOIN Message75 c ON u.ConnectCompany = c.Message_ID
            SET u.Created = c.Created
            WHERE
              (c.Created > u.Created)
              AND
              u.Created = :created
            ;',
            array(
                'created' => '0000-00-00'
            )
        );

        //?? Для оставшихся пользователей с Created = 0 проставляем дату '2011-01-01'
        $conn->executeUpdate(
            '
            UPDATE User
            SET Created = :newDate
            WHERE
               Created = :created
            ;',
            array(
                'created' => '0000-00-00',
                'newDate' => $magicDate
            )
        );

        //?? Для таблицы company_log для несуществующих пользователей-редакторов проставляем 0
        $conn->executeUpdate(
            '
            UPDATE `company_log`
            SET `updated_by` = :updated
            WHERE
                `updated_by` NOT IN (
                    SELECT `User_ID`
                    FROM User
                    )
            ;',
            array(
                'updated' => NULL
            )
        );

        //?? Для таблицы company_log пользователей для несуществующих пользователей-создателей проставляем id существующего привязанного или id пользователя-редактора
        $companyLogNoUser = $conn->fetchAll('SELECT *
                                FROM `company_log`
                                WHERE `created_by` NOT
                                IN (
                                SELECT `User_ID`
                                FROM User
                                )
                                and `created_by`<> 0
                               ');
        foreach($companyLogNoUser as $companyNoUser){
            $newUser = 0;//$companyNoUser['updated_by'];
            $user = $conn->fetchAll('SELECT min(`User_ID`) uid FROM `User` WHERE `ConnectCompany` = :company GROUP BY `ConnectCompany`', array('company' => $companyNoUser['company_id']));
            if ($user){
                $newUser = $user[0]['uid'];
            }
            $conn->executeUpdate('
                UPDATE `company_log` SET `created_by` = :newid WHERE `company_id` = :company
                ',
                array('newid' => $newUser, 'company' => $companyNoUser['company_id'] ));
        }

        //Обновляем дату создания продута у которого Created = 0 или < даты создания компании до даты создания компании
        $conn->executeUpdate('
               UPDATE Message142 prod
        JOIN Message75 comp
        ON prod.Company_ID = comp.Message_ID
        SET prod.Created = comp.Created
        WHERE prod.Created < comp.Created
          AND comp.Created > :companyCreated',
            array(
                'companyCreated' => 0
            )
        );

        //Удаляем привязку компании к разделам, в которых не разрешены продукты или с несуществующими разделами
        $conn->executeQuery('
                DELETE cc
                  FROM `Message76` cc
                  WHERE NOT EXISTS
                        (
                        SELECT c.`Message_ID`
                        FROM `Message73` c
                        WHERE cc.cat_id = c.Message_ID AND c.`allow_products` = 1 AND c.`Checked` = 1
                        )'
        );

        //Заодно удаляем привязки для уже несуществующих компаний
        $conn->executeQuery('
                DELETE
                  FROM `Message76`
                  WHERE `company_id`
                        NOT IN (
                        SELECT `Message_ID`
                        FROM `Message75`
                        )'
        );

        //Удаляем фото, привязанные к уже несуществующим компаниям
        $conn->executeQuery('
                DELETE
                  FROM `Companies_images`
                  WHERE `Company_ID`
                        NOT IN (
                        SELECT `Message_ID`
                        FROM `Message75`
                        )'
        );

        //Заодно в NULL в продуктах отсутствующие image_id
        $conn->executeUpdate('
                UPDATE `Message142`
                SET Image_ID = :val
                WHERE Image_ID NOT IN (SELECT ID FROM Companies_images)', array('val' => NULL));


        //Товары привязаны к несуществующим компаниям
        $conn->executeUpdate('
                UPDATE `Message142`
                SET `Company_ID` = :val, `Checked` = :del
                WHERE `Company_ID` NOT IN (SELECT `Message_ID` FROM `Message75`)', array('val' => NULL, 'del' => 2));

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
