<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Metal\ProjectBundle\Entity\BanIP;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20150803145744 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->connection->getConfiguration()->setSQLLogger(null);

        $badIps = array(
            '46.161.41.199' => true,
            '89.255.92.151' => true,
            '46.161.41.34' => true,
            '95.46.97.61' => true,
            '95.46.99.159' => true,
            '95.46.99.103' => true,
            '95.46.97.73' => true,
            '95.46.99.171' => true,
            '95.46.99.147' => true,
            '95.46.97.105' => true,
            '95.46.97.209' => true,
            '148.251.236.167' => true,
            '88.198.180.41' => true,
            '144.76.27.118' => true,
            '144.76.15.235' => true,
            '78.46.174.55' => true,
            '144.76.63.35' => true,
            '92.36.46.229' => true,
            '5.9.151.67' => true,
            '81.91.33.75' => true,
            '208.115.111.72' => true,
        );

        foreach ($badIps as $ip => $status) {
            $this->addSql(
                'REPLACE INTO ban_ip (int_ip, ip, hostname, created_at, status) VALUES(:int_ip, :ip, :hostname, :created_at, :status)',
                array(
                    'int_ip' => ip2long($ip),
                    'ip' => $ip,
                    'hostname' => gethostbyaddr($ip),
                    'created_at' => new \DateTime(),
                    'status' => BanIP::STATUS_BLOCKED_MANUALLY
                ),
                array('created_at' => 'datetime')
            );
        }

        $goodIps = array(
            '5.141.101.191' => true,
            '188.226.86.162' => true,
        );

        foreach ($goodIps as $ip => $status) {
            $this->addSql(
                'REPLACE INTO ban_ip (int_ip, ip, hostname, created_at, status) VALUES(:int_ip, :ip, :hostname, :created_at, :status)',
                array(
                    'int_ip' => ip2long($ip),
                    'ip' => $ip,
                    'hostname' => gethostbyaddr($ip),
                    'created_at' => new \DateTime(),
                    'status' => BanIP::STATUS_WHITELISTED_MANUALLY
                ),
                array('created_at' => 'datetime'));
        }
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
