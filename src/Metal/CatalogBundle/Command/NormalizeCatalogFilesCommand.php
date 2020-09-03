<?php

namespace Metal\CatalogBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class NormalizeCatalogFilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:catalog:normalize-files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $tablesToNormalize = array(
            'catalog_product' => array(
                'subdir' => 'catalog_products',
                'name' => 'photo_name',
                'mime' => 'photo_mime_type'
            ),
            'catalog_manufacturer' => array(
                'subdir' => 'catalog_manufacturer',
                'name' => 'logo_name',
                'mime' => 'logo_mime_type'
            ),
            'catalog_brand' => array(
                'subdir' => 'catalog_brand',
                'name' => 'logo_name',
                'mime' => 'logo_mime_type'
            ),
        );
        $extensionGuesser = ExtensionGuesser::getInstance();
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        foreach ($tablesToNormalize as $tableName => $fields) {
            $files = $conn->fetchAll(sprintf(
                "
                    SELECT t.id, t.%s, t.%s FROM %s as t 
                    WHERE t.%s IS NOT NULL
                    ORDER BY t.id ASC
                    ",
                    $fields['name'],
                    $fields['mime'],
                    $conn->quoteIdentifier($tableName),
                    $fields['name']
                )

            );

            foreach ($files as $file) {
                $entityId = $file['id'];
                $filePath = $uploadDir.'/'.$fields['subdir'].'/'.$file[$fields['name']];

                if (!$file[$fields['name']] || !file_exists($filePath)) {
                    $output->writeln(sprintf('File not exists %s file %d"', $tableName, $entityId));
                    continue;
                }

                $output->writeln(sprintf('Move %s file %d"', $tableName, $entityId));

                $mime = $file[$fields['mime']];
                $extension = '';
                if (!$mime) {
                    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                }
                $name = sha1(microtime(true).mt_rand(0, 9999)).'.'.($extension ?:$extensionGuesser->guess($mime));
                try {
                    $newFilePath = $uploadDir.'/'.$fields['subdir'].'/'.substr($name, 0, 2).'/'.$name;

                    if (!is_dir($dir = dirname($newFilePath))) {
                        mkdir($dir, 0777, true);
                    }

                    rename($filePath, $newFilePath);

                    $conn->executeUpdate(sprintf(
                        "UPDATE %s SET %s = :name WHERE id = :id",
                        $conn->quoteIdentifier($tableName),
                        $fields['name']
                    ),
                        array('name' => $name, 'id' => $entityId)
                    );
                } catch (\Exception $e) {
                    $output->writeln(sprintf('Can`t move file "%s" %d', $tableName, $entityId));
                }
            }
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
