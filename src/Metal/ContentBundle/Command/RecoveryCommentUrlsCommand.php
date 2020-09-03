<?php

namespace Metal\ContentBundle\Command;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RecoveryCommentUrlsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:content:recovery-comment-urls');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine');
        /** @var $em EntityManager */

        $imageTypes = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image')->getImageTypes();

        $conn = $em->getConnection();
        /* @var $conn Connection */

        $entries = $conn->fetchAll('SELECT content_entry_id, description FROM content_comment');

        $patterns = array(
            '\[url=(.*?)](?:.*?)\[\/url\]' => 'need_replace',
            '\[url\](.*?)\[\/url\]' => 'need_replace',
        );

        $routerGenerator = $this->getContainer()->get('router');
        
        $getProjectUrl = function ($source, $id) use (&$em, &$routerGenerator) {
            $entity = null;

            switch ($source) {
                case 'publications':
                    $entity = $em->getRepository('MetalContentBundle:Topic')->findOneBy(array('id' => $id));
                    $route = 'MetalContentBundle:Topic:view';

                    if (!$entity) {
                        return null;
                    }

                    $param = array('id' => $entity->getId(), 'category_slug' => $entity->getCategory()->getSlugCombined());
                    break;

                case 'questions':
                    $entity = $em->getRepository('MetalContentBundle:Question')->findOneBy(array('id' => $id));
                    $route = 'MetalContentBundle:Question:view';

                    if (!$entity) {
                        return null;
                    }

                    $param = array('id' => $entity->getId(), 'category_slug' => $entity->getCategory()->getSlugCombined());

                    break;

                case 'tag':
                    $entity = $em->getRepository('MetalContentBundle:Tag')->findOneBy(array('id' => $id));
                    $route = 'MetalContentBundle:Tag:view';

                    if (!$entity) {
                        return null;
                    }

                    $param = array('id' => $entity->getId());

                    break;

                default:
                    return null;
            }

            return $routerGenerator->generate($route, $param, true);
        };

        foreach ($entries as $entry) {
            $description = $entry['description'];
            $needRefresh = false;
            foreach ($patterns as $pattern => $action) {

                preg_match('/'.$pattern.'/ui', $description, $matches);

                if (empty($matches[1])) {
                   continue;
                }

                foreach ($imageTypes as $imageType) {
                    if (false !== stripos($matches[1], $imageType)) {
                        $output->writeln(sprintf('%s: Found image url "%s"', date('d.m.Y H:i:s'), $matches[0]));
                        continue 2;
                    }
                }

                if ($action === 'need_replace') {
                    preg_match('/(publications|questions|tag)_(\d+)/ui', $matches[0], $matchUrl);

                    if (empty($matchUrl[0])) {
                        $output->writeln(sprintf('%s: Not publications|questions|tag in url "%s"', date('d.m.Y H:i:s'), $matches[0]));

                        continue;
                    }

                    $url = $getProjectUrl($matchUrl[1], $matchUrl[2]);

                    if (null === $url) {
                        $output->writeln(sprintf('%s: Don\'t match url "%s"', date('d.m.Y H:i:s'), $matches[0]));

                        continue;
                    }
                    
                    if ($matches[1] === $url) {
                        $output->writeln(sprintf('%s: Url identical', date('d.m.Y H:i:s')));
                        
                        continue;
                    }
                    
                    $description = str_replace($matches[1], $url, $description);

                    $needRefresh = true;
                    $output->writeln(sprintf('%s: Before: <comment>"%s"</comment> ------ After: <info>"%s"</info>', date('d.m.Y H:i:s'), $matches[1], $url));
                }
            }

            if ($needRefresh) {
                $conn->executeUpdate('UPDATE content_comment SET description = :description WHERE content_entry_id = :id',
                    array('description' => $description, 'id' => $entry['content_entry_id']));
            }
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
