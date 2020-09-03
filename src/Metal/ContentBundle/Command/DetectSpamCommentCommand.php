<?php

namespace Metal\ContentBundle\Command;

use Buzz\Exception\RequestException;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class DetectSpamCommentCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:content:detect-spam-comment');
        $this->addOption('comment-id', null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));


        $em = $this->getContainer()->get('doctrine');
        /** @var $em EntityManager */

        $conn = $em->getConnection();
        /* @var $conn Connection */

        # создаю вручную request и делаю set
        $request = new Request();
        $request->create('/');
        $this->getContainer()->enterScope('request');
        $this->getContainer()->set('request', $request, 'request');
        #
        $akismet = $this->getContainer()->get('ornicar_akismet');
        $count = 0;
        $skip = 0;
        $limit = 100;

        $minId = $input->getOption('comment-id') ?: $conn->fetchColumn('SELECT MIN(id) FROM content_comment');
        $maxId = $conn->fetchColumn('SELECT MAX(id) FROM content_comment');

        do {
            $comments = $conn->createQueryBuilder()
                ->select('c.id, c.description, c.name')
                ->from('content_comment', 'c')
                ->addSelect('u.ForumName')
                ->leftJoin('c', 'User', 'u', 'c.user_id = u.User_ID')
                ->orderBy('c.id', 'ASC')
                ->where('c.id >= :min_id')
                ->setParameter('min_id', $minId)
                ->setMaxResults($limit)
                ->setFirstResult($skip)
                ->execute()
                ->fetchAll();;
            $skip += $limit;

            foreach ($comments as $comment) {
                try {
                    $isSpam = $akismet->isSpam(
                        array(
                            'comment_author' => $comment['ForumName'] ?: $comment['name'],
                            'comment_content' => $comment['description'],
                        )
                    );

                    if ($isSpam) {
                        $count++;
                        $conn->executeUpdate(
                            '
                        UPDATE content_comment
                        SET status_type_id = 5
                        WHERE id = :id',
                            array('id' => $comment['id'])
                        );
                    }
                } catch (RequestException $e) {
                    $output->writeln('Last comment-id:'.$comment['id']);
                    break 2;
                }

                $minId = $comment['id'];
            }

            $output->writeln('Completed:'.$skip);
            $output->writeln('Count spam comments:'.$count);

            if ($minId == $maxId) {
                break;
            }
        } while ($comments);

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }
}
