<?php

namespace Metal\DemandsBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MirrorDemandsToStroyCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:demands:mirror-demands-to-stroy');
    }
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));
        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);

        $conn = $em->getConnection();
        /* @var $conn Connection */
        $conn->getConfiguration()->setSQLLogger(null);

        $parametersMetalloprokat = $this->getContainer()->getParameter('database_metalloprokat');
        $metalloprokatConn = $this->getContainer()->get('doctrine.dbal.connection_factory')->createConnection($parametersMetalloprokat);
        /* @var $metalloprokatConn Connection */

        $maxMirroredAt = $conn->fetchColumn('SELECT MAX(mirrored_at) FROM demand_mirror');

        if ($maxMirroredAt == null) {
            $maxMirroredAt = new \DateTime('0000-00-00 00:00:00');
        } else {
            $maxMirroredAt = new \DateTime($maxMirroredAt);
        }

        $demandsToMirror = $metalloprokatConn->fetchAll('
            SELECT * FROM demand WHERE demand_type = :type_public
            AND updated_at > :max_mirrored_at
            AND mirror_to_stroy = TRUE
            AND moderated_at IS NOT NULL 
            AND deleted_at IS NULL
            ORDER BY updated_at ASC
            ',
            array('type_public' => AbstractDemand::TYPE_PUBLIC, 'max_mirrored_at' => $maxMirroredAt),
            array('max_mirrored_at' => 'datetime')

        );

        $blacklist = array(
            'demand' => array(
                'id',
                'from_callback_id',
                'updated_by',
                'category_id',
                'views_count',
                'referer',
                'moderated_at',
                'answers_count',
                'from_demand_id',
                'possible_user_id',
                'confirmation_code',
                'product_city_id',
                'display_file_on_site',
                'parsed_demand_id',
                'product_country_id',
                'public_until',
                'fake_updated_at',
                'viewed_by',
                'viewed_at',
                'user_id',
                'mirror_to_stroy',
                'company_id'
                ),
            'demand_item' => array(
                'id',
                'demand_id',
                'category_id'
            ),
            'demand_file' => array(
                'id',
                'demand_id'
            )
        );

        foreach ($demandsToMirror as $demandToMirror) {
            $demandToUpdateId = (int)$conn->fetchColumn('SELECT demand_id FROM demand_mirror WHERE original_demand_id = :original_id',
                array('original_id' => (int)$demandToMirror['id'])
            );

            if ($demandToUpdateId) {
                $output->writeln(sprintf('Update demand_mirror, demand_id: "%d"', $demandToUpdateId));
                $conn->executeUpdate('UPDATE demand_mirror SET mirrored_at = :_updated_at WHERE demand_id = :_demand_id',
                    array('_updated_at' => $demandToMirror['updated_at'], '_demand_id' => $demandToUpdateId)
                );

                continue;
            }
            $conn->beginTransaction();
            $demandToMirrorWhite = array_diff_key($demandToMirror, array_flip($blacklist['demand']));
            $demandToMirrorWhite['source_type'] = SiteSourceTypeProvider::SOURCE_MIRROR_FROM_METALLOPROKAT;
            $conn->insert('demand', $demandToMirrorWhite);

            $demandFiles = $metalloprokatConn->fetchAll('SELECT * FROM demand_file WHERE demand_id = :demand_id',
                array('demand_id' => (int)$demandToMirror['id'])
            );

             $demandItems = $metalloprokatConn->fetchAll('SELECT * FROM demand_item WHERE demand_id = :demand_id',
                array('demand_id' => (int)$demandToMirror['id'])
            );

            $demandInStroyId = $conn->lastInsertId();
            $output->writeln(sprintf('Inserted demand: "%d" and items', $demandInStroyId));
            foreach ($demandItems as $demandItem) {
                $demandItemToMirrorWhite = array_diff_key($demandItem, array_flip($blacklist['demand_item']));
                $demandItemToMirrorWhite['demand_id'] = $demandInStroyId;
                $conn->insert('demand_item', $demandItemToMirrorWhite);
            }

            $output->writeln(sprintf('Inserted demand: "%d" and files', $demandInStroyId));
            foreach ($demandFiles as $demandFile) {
                $demandFileId = $demandFile['id'];
                $demandFileName = $demandFile['name'];
                $demandFileToMirrorWhite = array_diff_key($demandFile, array_flip($blacklist['demand_file']));
                $demandFileToMirrorWhite['demand_id'] = $demandInStroyId;
                $conn->insert('demand_file', $demandFileToMirrorWhite);
                $this->moveDemandFile($demandFileName, $demandFileId);
            }

            $mirrorDemand = array(
                'demand_id' => $demandInStroyId,
                'original_demand_id' => $demandToMirror['id'],
                'mirrored_at' => $demandToMirror['updated_at']
            );

            $output->writeln(sprintf('Inserted demand_mirror by demand_id: "%d"', $demandInStroyId));
            $conn->insert('demand_mirror', $mirrorDemand);
            $demandInStroyId = null;
            $conn->commit();
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    private function moveDemandFile($fileName, $demandFileId)
    {
        $metalloprokatPath = $this->getContainer()->getParameter('authentication_server_url');

        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        $dir = $uploadDir.'/demands/';
        if (!is_dir($dir)) {
            mkdir($dir);
        }

        $pieces = array();
        for ($i = 0; $i <= 1; $i++) {
            $pieces[] = mb_substr($fileName, $i * 2, 2);
        }

        $subDir = implode('/', $pieces);
        $dirTo = $dir.$subDir;

        if (!is_dir($dirTo)) {
            mkdir($dirTo, 0777, true);
        }

        $options = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true,
                'verify_depth' => 0
            )
        );

        $streamContext = stream_context_create($options);

        copy(
            $metalloprokatPath.'demands/download/'.$demandFileId,
            $dirTo.'/'.$fileName,
            $streamContext
        );
    }
}
