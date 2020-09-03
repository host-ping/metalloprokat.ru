<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150116171445 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->addSql("
        ALTER TABLE Message76
            DROP User_ID,
            DROP Subdivision_ID,
            DROP Sub_Class_ID,
            DROP Priority,
            DROP Keyword,
            DROP Checked,
            DROP TimeToDelete,
            DROP TimeToUncheck,
            DROP IP,
            DROP UserAgent,
            DROP Parent_Message_ID,
            DROP LastUpdated,
            DROP LastUser_ID,
            DROP LastIP,
            DROP LastUserAgent,
            DROP parent_cat
        ");

        $this->addSql("DROP INDEX company_id_2 ON Message76");

        $this->addSql("DROP INDEX parent_cat_2 ON Message76");


        $companyCategories = $this->connection->executeQuery(
            '
                SELECT Message_ID, company_id, cat_id
                FROM Message76
                GROUP BY company_id, cat_id
                HAVING COUNT(company_id) > 1;
            '
        )->fetchAll();

        foreach ($companyCategories as $companyCategory) {
            $this->connection->executeUpdate(
                'DELETE FROM Message76 WHERE company_id = :company_id AND cat_id = :category_id AND Message_ID != :id',
                array(
                    'company_id' => $companyCategory['company_id'],
                    'category_id' => $companyCategory['cat_id'],
                    'id' => $companyCategory['Message_ID']
                )
            );
        }


        $this->addSql("CREATE UNIQUE INDEX UNIQ_company_category ON Message76 (company_id, cat_id)");

        $this->addSql('DELETE FROM Message76 WHERE NOT EXISTS (SELECT * FROM Message75 AS company WHERE company.Message_ID = company_id)');
        $this->addSql('DELETE FROM Message76 WHERE NOT EXISTS (SELECT * FROM Message73 AS category WHERE cat_id = category.Message_ID)');

        $this->addSql("ALTER TABLE Message76 ADD CONSTRAINT FK_12EB46D9979B1AD6 FOREIGN KEY (company_id) REFERENCES Message75 (Message_ID)");

        $this->addSql("ALTER TABLE Message76 ADD CONSTRAINT FK_12EB46D9E6ADA943 FOREIGN KEY (cat_id) REFERENCES Message73 (Message_ID) ON DELETE CASCADE");

    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
