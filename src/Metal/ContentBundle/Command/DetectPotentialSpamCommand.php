<?php

namespace Metal\ContentBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use mxkh\url\UrlFinder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class DetectPotentialSpamCommand extends ContainerAwareCommand
{
    const LIMIT_ROWS = 100;

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var UrlFinder
     */
    protected $urlFinder;

    /**
     * @var array
     */
    protected $imageTypes;

    protected function configure()
    {
        $this->setName('metal:content:detect-potential-spam');
        $this->addOption('process', null, InputOption::VALUE_OPTIONAL|InputOption::VALUE_IS_ARRAY, null, array('entry', 'comments'));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $this->connection = $this->getContainer()->get('doctrine')->getConnection();
        $this->input = $input;
        $this->output = $output;
        $this->urlFinder = new UrlFinder();
        $this->imageTypes = $this->getContainer()->get('brouzie.helper_factory')->get('MetalProjectBundle:Image')->getImageTypes();

        if (true === in_array('comments', $input->getOption('process'), true)) {
            $this->processContentComments();
        }

        if (true === in_array('entry', $input->getOption('process'), true)) {
            $this->processContentEntry();
        }


        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

    protected static function recoveryUrl($string = '')
    {
        $string = trim($string);

        if (!$string) {
            return $string;
        }

        $replacementPatterns = array(
            '#http://http://#ui' => 'http://',
            '#tags_(\d+).html#ui' => 'tag_$1.html',
            '#((\[url=)?https?://)(?!www\.stroy|stroy\.)(.*?)([a-zа-я ]*?)(\[\/url\])?#ui' => 'http://www.stroy.ru/'
        );

        foreach ($replacementPatterns as $pattern => $replacementPattern) {
            $string = preg_replace($pattern, $replacementPattern, $string);
        }

        return $string;
    }

    protected static function isTextChange($newText, $oldText)
    {
        return $newText !== $oldText;
    }

    protected function processContentEntry()
    {
        $countForEntry = 0;
        $minIdForEntry = 0;
        do {
            $entries = $this->connection->createQueryBuilder()
                ->select('e.content_entry_id, e.short_description, e.description')
                ->from('content_entry', 'e')
                ->orderBy('e.content_entry_id', 'ASC')
                ->where('e.content_entry_id > :min_id')
                ->andWhere('e.status_type_id IN (:statuses)')
                ->setParameter('statuses', self::getAllowedStatuses(), Connection::PARAM_INT_ARRAY)
                ->setParameter('min_id', $minIdForEntry)
                ->setMaxResults(self::LIMIT_ROWS)
                ->execute()
                ->fetchAll();

            foreach ($entries as $key => $entry) {

                $descriptionEntry = self::recoveryUrl($entry['description']);
                $shortDescriptionEntry = self::recoveryUrl($entry['short_description']);

                if (true === $this->isPotentialSpam($descriptionEntry) || true === $this->isPotentialSpam($shortDescriptionEntry)) {
                    $countForEntry++;
                    $this->connection->executeUpdate('
                        UPDATE content_entry
                        SET status_type_id = :type
                        WHERE content_entry_id = :id',
                        array(
                            'id' => $entry['content_entry_id'],
                            'type' => StatusTypeProvider::POTENTIAL_SPAM
                        )
                    );
                }

                if (self::isTextChange($descriptionEntry, $entry['description']) || self::isTextChange($shortDescriptionEntry, $entry['short_description'])) {
                    $this->connection->executeUpdate('
                        UPDATE content_entry
                        SET description = :entry_description_new,
                            short_description = :short_description_new
                        WHERE content_entry_id = :id',
                        array('id' => $entry['content_entry_id'], 'entry_description_new' => $descriptionEntry, 'short_description_new' => $shortDescriptionEntry)
                    );
                }

                $minIdForEntry = $entry['content_entry_id'];
            }

            $this->output->writeln('Completed:' . $minIdForEntry);
            $this->output->writeln('Count potential spam entries:' . $countForEntry);

        } while ($entries);
    }

    protected function processContentComments()
    {
        $count = 0;
        $minId = 0;

        do {
            $comments = $this->connection->createQueryBuilder()
                ->select('c.id, c.description, c.name')
                ->from('content_comment', 'c')
                ->orderBy('c.id', 'ASC')
                ->where('c.id > :min_id')
                ->andWhere('c.status_type_id IN (:statuses)')
                ->setParameter('statuses', self::getAllowedStatuses(), Connection::PARAM_INT_ARRAY)
                ->setParameter('min_id', $minId)
                ->setMaxResults(self::LIMIT_ROWS)
                ->execute()
                ->fetchAll();

            foreach ($comments as $key => $comment) {
                $description = self::recoveryUrl($comment['description']);

                if (true === $this->isPotentialSpam($description)) {
                    $count++;
                    $this->connection->executeUpdate('
                        UPDATE content_comment
                        SET status_type_id = :type
                        WHERE id = :id',
                        array(
                            'id' => $comment['id'],
                            'type' => StatusTypeProvider::POTENTIAL_SPAM
                        )
                    );
                }

                if (self::isTextChange($description, $comment['description'])) {
                    $this->connection->executeUpdate('
                        UPDATE content_comment
                        SET description = :description_new
                        WHERE id = :id',
                        array('id' => $comment['id'], 'description_new' => $description)
                    );   
                }

                $minId = $comment['id'];
            }

            $this->output->writeln('Completed:' . $minId);
            $this->output->writeln('Count potential spam comments:' . $count);

        } while ($comments);
    }

    protected function isPotentialSpam($string = '')
    {
        $string = trim($string);

        if (!$string) {
            return null;
        }

        $allUrls = $this->urlFinder->url->subject($string)->all();

        if (null === $allUrls) {
            return null;
        }

        $isSpam = false;
        foreach ($allUrls[0] as $allUrl) {

            if (true === $this->isImageLink($allUrl)) {
                continue;
            }

            $isMatch = false;
            foreach (self::getAllowedWords() as $allowedWord) {
                if (false !== stripos($allUrl, $allowedWord)) {
                    $isMatch = true;
                }
            }
            
            if ($isMatch === false) {
                $isSpam = true;
            }

            if ($isSpam === true) {
                $this->output->writeln(sprintf('%s: Found potential spam url: "<info>%s</info>"', date('d.m.Y H:i:s'), $allUrl));
                sleep(1);
                return $isSpam;
            }
        }

        return $isSpam;
    }

    protected function isImageLink($url)
    {
        foreach ($this->imageTypes as $imageType) {
            if (false !== stripos($url, $imageType)) {
                $this->output->writeln(sprintf('%s: Found image url "%s"', date('d.m.Y H:i:s'), $url));
                return true;
            }
        }

        return false;
    }

    protected static function getAllowedWords()
    {
        return array(
            'stroy.ru',
            'youtube.com',
            'rutube.ru',
            'coub.com',
            'vimeo.com',
            'wikipedia.org',
            'nayada.ru',
            'height:',
            'size:',
            'width:',
            'left:',
            'padding:',
            'align:',
            'margin:'
        );
    }

    protected static function getAllowedStatuses()
    {
        return array(
            StatusTypeProvider::NOT_CHECKED,
            StatusTypeProvider::CHECKED
        );
    }
}
