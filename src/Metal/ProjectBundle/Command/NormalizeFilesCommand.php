<?php

namespace Metal\ProjectBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

class NormalizeFilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:project:normalize-files');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $doctrine = $this->getContainer()->get('doctrine');
        $em = $doctrine->getManager();
        /* @var  $em EntityManager */
        $conn = $em->getConnection();

        $tablesToNormalize = array(
            'company_file' => array(
                'subdir' => 'company_documents',
                'old_subdir' => 'company-documents',
                'special_subdir' => 'company_id',
                'name' => 'file_name',
                'mime' => 'file_mime_type'
            ),
            'landing_template' => array(
                'subdir' => 'landing_templates',
                'name' => 'file_name',
                'mime' => 'file_mime_type'
            ),
            'mini_site_cover' => array(
                'subdir' => 'cover',
                'name' => 'file_name',
                'mime' => 'file_mime_type'
            ),
            'company_payment_details' => array(
                'subdir' => 'payment_details',
                'old_subdir' => 'payment-details',
                'name' => 'file_name',
                'mime' => 'file_mime_type'
            ),
            'company_registration' => array(
                'subdir' => 'company_registration_price',
                'name' => 'price_name',
                'mime' => 'price_mime_type'
            ),
        );
        $extensionGuesser = ExtensionGuesser::getInstance();
        $uploadDir = $this->getContainer()->getParameter('upload_dir');

        foreach ($tablesToNormalize as $tableName => $fields) {
            $files = $conn->fetchAll(sprintf(
                    "
                    SELECT t.id, t.%s, t.%s, t.%s FROM %s as t 
                    WHERE t.%s IS NOT NULL
                    ORDER BY t.id ASC
                    ",
                    $fields['name'],
                    $fields['mime'],
                    isset($fields['special_subdir']) ? $fields['special_subdir'] : $fields['name'],
                    $conn->quoteIdentifier($tableName),
                    $fields['name']
                )

            );

            foreach ($files as $file) {
                $entityId = $file['id'];
                $oldSubdir = isset($fields['old_subdir']) ? $fields['old_subdir'] : $fields['subdir'];
                if (isset($fields['special_subdir'])) {
                    $oldSubdir .= '/'.$file[$fields['special_subdir']];
                }
                $subdir = $fields['subdir'];

                $filePath = $uploadDir.'/'.$oldSubdir.'/'.$file[$fields['name']];

                if (!$file[$fields['name']]) {
                    $output->writeln(sprintf('File not selected %s file %d"', $tableName, $entityId));
                    continue;
                }
                if (!file_exists($filePath)) {
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
                    $newFilePath = $uploadDir.'/'.$subdir.'/'.substr($name, 0, 2).'/'.$name;

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
