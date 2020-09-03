<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160331182600 extends AbstractMigration implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        if ($this->container->getParameter('project.family') === 'stroy') {
            $this->addSql("UPDATE instagram_photo SET `code` = SUBSTRING(url, 29)");

            $instagram = $this->container->get('metal.content.instagram');
            try {
                $instagram->login();
            } catch (\InstagramException $e) {
                exit();
            }

            $em = $this->container->get('doctrine')->getManager();
            /** @var $em EntityManager */

            $feed = $instagram->getSelfUserFeed();
            $instagramPhotoRepository = $em->getRepository('MetalContentBundle:InstagramPhoto');

            foreach ($feed['items'] as $post) {
                if ($instagramPhoto = $instagramPhotoRepository->findOneBy(array('code' => $post['code']))) {
                    $instagramPhoto->setRepostId($post['id']);
                }

                $em->flush();
            }
        }
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
