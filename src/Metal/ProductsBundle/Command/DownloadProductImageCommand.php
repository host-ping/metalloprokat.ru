<?php

namespace Metal\ProductsBundle\Command;

use Buzz\Browser;
use Buzz\Bundle\BuzzBundle\Buzz\Buzz;
use Doctrine\ORM\EntityManager;
use Metal\GrabbersBundle\Grabber\GrabberHelper;
use Metal\ProductsBundle\Entity\ProductImage;
use Metal\ProjectBundle\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\Url;
use Metal\ProjectBundle\Doctrine\Utils;

class DownloadProductImageCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:products:download-images');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, '', 500);
        $this->addOption('max-retries-count', null, InputOption::VALUE_OPTIONAL, '', 5);
        $this->addOption('batch-size', null, InputOption::VALUE_OPTIONAL, '', 5);
        $this->addOption('company-id', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine')->getManager();
        /* @var $em EntityManager */
        $buzz = $this->getContainer()->get('buzz');
        /* @var $buzz Buzz */
        $browser = $buzz->getBrowser('downloader');
        /* @var $browser Browser */
        $browser->getClient()->setVerifyPeer(false);
        $browser->getClient()->setTimeout(10);

        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */

        $imageTypes = $imageHelper->getImageTypes();
        $guesser = MimeTypeGuesser::getInstance();

        $productImagesQb = $em->getRepository('MetalProductsBundle:ProductImage')->createQueryBuilder('pi')
            ->where('pi.url IS NOT NULL')
            ->andWhere('pi.downloaded = false');

        if ($companyId = $input->getOption('company-id')) {
            $productImagesQb
                ->andWhere('pi.company = :company_id')
                ->setParameter('company_id', $companyId);
        }

        $maxRetriesCount = $input->getOption('max-retries-count') + 0;
        if ($maxRetriesCount) {
            $productImagesQb->andWhere('pi.retriesCount < :max_retries_count')
                ->setParameter('max_retries_count', $maxRetriesCount);
        }

        $limit = $input->getOption('limit') + 0;
        $batchSize = $input->getOption('batch-size') + 0;

        if ($limit) {
            $productImagesQb->setMaxResults($limit);
        }

        $productImages = $productImagesQb
            ->addOrderBy('pi.updatedAt', 'DESC')
            ->getQuery()
            ->getResult();
        /* @var $productImages ProductImage[] */

        /**
         * @param ProductImage[] $productsImagesToProcess
         * @param array $tmpImagesFiles
         */
        $commit = function(array &$productsImagesToProcess, array &$tmpImagesFiles) use ($em, $output) {
            Utils::checkEmConnection($em);
            $em->flush();

            if ($productsImagesToProcess) {
                $output->writeln(sprintf('Скачано фото: %d', count($productsImagesToProcess)));
            }

            $productsImagesToProcess = array();
            $tmpImagesFiles = array();
        };

        $i = 0;
        $productsImagesToProcess = array();
        $tmpImagesFiles = array();
        $validator = $this->getContainer()->get('validator');

        foreach ($productImages as $productImage) {
            $i++;
            $tmpImagesFiles[$productImage->getId()] = tmpfile();
            $pathToTmpImageFile = stream_get_meta_data($tmpImagesFiles[$productImage->getId()])['uri'];

            $output->writeln(
                sprintf(
                    'Пытаемся получить изображение ProductImage.id=%d, url = "%s" попыток %d',
                    $productImage->getId(),
                    $productImage->getUrl(),
                    $productImage->getRetriesCount()
                )
            );

            $productImage->increaseRetriesCount();
            $productImage->setUpdatedAt(new \DateTime());

            try {
                $errors = $validator->validate($productImage->getUrl(), array(new Url()));
                if (count($errors)) {
                    //TODO: mark url is invalid and show log

                    continue;
                }

                $response = $browser->get($productImage->getUrl(), array('User-Agent' => GrabberHelper::USER_AGENT));
                fwrite($tmpImagesFiles[$productImage->getId()], $response->getContent());
                fseek($tmpImagesFiles[$productImage->getId()], 0);

                if (!is_array(@getimagesize($pathToTmpImageFile))) {
                    $output->writeln(
                        sprintf(
                            'URL не является ссылкой на изображения. ID: %s URL: %s',
                            $productImage->getId(),
                            $productImage->getUrl()
                        )
                    );

                    continue;
                }

                $mimeType = $guesser->guess($pathToTmpImageFile);
                $imageObject = new UploadedFile($pathToTmpImageFile, urldecode(basename($productImage->getUrl())), $mimeType, filesize($pathToTmpImageFile), null, true);
                $productImage->setUploadedPhoto($imageObject);

                if (!isset($imageTypes[$imageObject->getMimeType()])) {
                    $output->writeln(
                        sprintf(
                            'Недопустимый MimeType: %s . ID: %s URL: %s',
                            $imageObject->getMimeType(),
                            $productImage->getId(),
                            $productImage->getUrl()
                        )
                    );

                    continue; //TODO: помечать файл как некорректный или удалять
                }
                $productImage->setDownloaded(true);
                $productsImagesToProcess[] = $productImage;

                if (0 === $i % $batchSize) {
                    $commit($productsImagesToProcess, $tmpImagesFiles);
                }

            } catch (\Exception $e) {
                $output->writeln(
                    sprintf(
                        'Не удалось получить изображение для ProductImage.id=%d, url = "%s" попыток %d , ошибка: "%s"',
                        $productImage->getId(),
                        $productImage->getUrl(),
                        $productImage->getRetriesCount(),
                        $e->getMessage()
                    )
                );
            }
        }

        $commit($productsImagesToProcess, $tmpImagesFiles);

        $em->getRepository('MetalProductsBundle:Product')->removeProductsLinksWithBadImage();

        $output->writeln(sprintf('End command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
