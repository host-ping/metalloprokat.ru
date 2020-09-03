<?php

namespace Metal\ContentBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Metal\ContentBundle\Entity\InstagramPhoto;
use Metal\ContentBundle\Service\Grabber\Grabber;
use Metal\ContentBundle\Service\Grabber\GrabberService;
use Metal\ContentBundle\Service\Grabber\Model\Photo;
use Metal\ProjectBundle\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class DownloadInstagramPhotosCommand extends ContainerAwareCommand
{
    const DATE_FROM = '2016-05-28';

    protected function configure()
    {
        $this->setName('metal:content:download-instagram-photos');
        $this->addOption('limit', null, InputOption::VALUE_OPTIONAL, '', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var $em EntityManager */

        $conn = $em->getConnection();
        /* @var $conn Connection */

        $guesser = MimeTypeGuesser::getInstance();

        $imageHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image');
        /* @var $imageHelper ImageHelper */
        $grabber = $this->getContainer()->get('metal.content.grabber');

        $imageTypes = $imageHelper->getImageTypes();

        $usersWithTags = $conn->fetchAll("
            SELECT 
            ipu.name, 
            ipu.id, 
            GROUP_CONCAT(DISTINCT iput.title ORDER BY iput.title ASC SEPARATOR ',') AS tags
            FROM instagram_user AS ipu
            LEFT JOIN instagram_user_tag iput ON ipu.id = iput.user_id
            WHERE ipu.is_enabled = true
            GROUP BY ipu.id;"
        );

        //TODO: придумать как обрабатывать пользователей которые добавили новые теги к старым фото
        $photosByUserToDownloadData = array();
        foreach ($usersWithTags as $userWithTags) {
            $limit = $input->getOption('limit');
            foreach (explode(',', $userWithTags['tags']) as $tag) {
                $endCursor = null;
                foreach ($grabber->getProfilePhotos($userWithTags['name'], $endCursor) as $endCursor => $photo) {

                    $existPhoto = $conn->fetchColumn(
                        'SELECT 1 FROM instagram_photo WHERE photo_id = :photo_id',
                        array('photo_id' => $photo->getId())
                    );

                    if ($existPhoto) {
                        $output->writeln(sprintf('%s: Photo "%s" already exist.', date('d.m.Y H:i:s'), $photo->getId()));
                        continue;
                    }

                    if ($limit <= 0) {
                        break 3;
                    }

                    if (in_array(mb_strtoupper($tag), $photo->getAllTags(true))) {
                        if (!isset($photosByUserToDownloadData[$userWithTags['id']])) {
                            $photosByUserToDownloadData[$userWithTags['id']] = array();
                        }

                        $photosByUserToDownloadData[$userWithTags['id']][] = $photo;

                        $photoIds[] = $photo->getId();
                        --$limit;
                    } else {
                        $output->writeln(sprintf('%s: Tag "%s" not found in photo.', date('d.m.Y H:i:s'), $tag));
                    }
                }
            }
        }

        /**
         * @param InstagramPhoto[] $photosToProcess
         */
        $commit = function(array &$photosToProcess) use ($em, $output) {

            $em->flush();

            if ($photosToProcess) {
                $output->writeln(sprintf('Скачано фото: %d', count($photosToProcess)));
            }

            $photosToProcess = array();
        };

        $i = 0;
        $photosToProcess = array();
        foreach ($photosByUserToDownloadData as $userId => $photosToDownloadData) {
            /* @var $photosToDownloadData Photo[] */
            foreach ($photosToDownloadData as $photoToDownloadData) {
                $i++;
                $pathToTmpImageFile = self::savePhoto($photoToDownloadData);
                if (!is_array(@getimagesize($pathToTmpImageFile))) {
                    $output->writeln(
                        sprintf(
                            'URL не является ссылкой на изображения. Link: %s URL: %s',
                            $photoToDownloadData->getLink(),
                            $photoToDownloadData->getDisplaySrc()
                        )
                    );

                    continue;
                }

                $mimeType = $guesser->guess($pathToTmpImageFile);
                $imageObject = new UploadedFile($pathToTmpImageFile, urldecode(basename($photoToDownloadData->getDisplaySrc())), $mimeType, filesize($pathToTmpImageFile), null, true);

                if (!isset($imageTypes[$imageObject->getMimeType()])) {
                    $output->writeln(
                        sprintf(
                            'Недопустимый MimeType: %s . ID: %s URL: %s',
                            $imageObject->getMimeType(),
                            $photoToDownloadData->getLink(),
                            $photoToDownloadData->getDisplaySrc()
                        )
                    );

                    continue;
                }

                $instagramPhoto = new InstagramPhoto();
                $instagramPhoto->setPhotoId($photoToDownloadData->getId());
                $instagramPhoto->setUploadedPhoto($imageObject);
                $instagramPhoto->setTags($photoToDownloadData->getAllTags());
                $instagramPhoto->setDescription($photoToDownloadData->getCaption());
                $instagramPhoto->setCode($photoToDownloadData->getCode());
                $instagramPhoto->setUrl($photoToDownloadData->getLink());
                $instagramPhoto->setUser($em->getReference('MetalContentBundle:InstagramUser', $userId));
                $instagramPhoto->setCreatedAt(new \DateTime());
                $em->persist($instagramPhoto);

                $photosToProcess[] = $instagramPhoto;

                if (($i % 50) === 0) {
                    $commit($photosToProcess);
                }
            }
        }

        $commit($photosToProcess);

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    /**
     * @param $photo
     *
     * @return string
     */
    public static function savePhoto(Photo $photo)
    {
        $tmpPhotoFile = tempnam('/tmp', $photo->getId());
        $imageUrl = GrabberService::getPageData($photo->getDisplaySrc());
        $handle = fopen($tmpPhotoFile, 'w');
        fwrite($handle, $imageUrl);
        fclose($handle);

        return $tmpPhotoFile;
    }
}
