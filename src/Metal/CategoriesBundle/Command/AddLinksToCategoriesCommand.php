<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddLinksToCategoriesCommand extends ContainerAwareCommand
{
    public function configure()
    {
        $this
            ->setName('metal:categories:add-links')
            ->addArgument('mention-count', InputArgument::OPTIONAL, '', 3);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $requiredMentionCount = $input->getArgument('mention-count');

        $em = $this->getContainer()->get('doctrine.orm.default_entity_manager');
        /* @var $em EntityManager */
        $em->getConfiguration()->setSQLLogger(null);
        $em->getConnection()->getConfiguration()->setSQLLogger(null);
        $conn = $em->getConnection();

        $categoriesToMention = array();
        $sql = 'SELECT Message_ID cat
                FROM
                `Message73`
                WHERE
                `cat_parent` > 0';

        $catIds = $conn->fetchAll($sql);
        foreach ($catIds as $mentionCount) {
            $categoriesToMention[$mentionCount['cat']] = 0;
        }

        $friendIdsFormatted = array();

        $sql = 'SELECT `Category_ID`, `Value`,`Links` FROM `Category_friends`';
        $friendIds = $conn->fetchAll($sql);

        foreach ($friendIds as $mentionCount) {
            $arrIDs = explode(',', $mentionCount['Value']);
            foreach ($arrIDs as $ref) {
                if (array_key_exists($ref, $categoriesToMention)) {
                    $categoriesToMention[$ref]++;
                    $friendIdsFormatted[$mentionCount['Category_ID']]['Value'] = $mentionCount['Value'];
                    $friendIdsFormatted[$mentionCount['Category_ID']]['Links'] = $mentionCount['Links'];
                } else {
                    $output->writeln('Not to add to '.$ref);
                }
            }
        }
        asort($categoriesToMention);

        $catIdsToUpdate = array_keys($friendIdsFormatted);
        foreach ($categoriesToMention as $categoryId => $mentionCount) {
            if ($mentionCount >= $requiredMentionCount) {
                break;
            }
            $addCount = $requiredMentionCount - $mentionCount;
            $output->writeln(sprintf('%s added %s times to:', $categoryId, $addCount));
            if ($addCount == 1) {
                $insToArr = array(array_rand($catIdsToUpdate, $addCount));
            } else {
                $insToArr = array_rand($catIdsToUpdate, $addCount);
            }

            foreach ($insToArr as $insCat) {
                if (array_key_exists($catIdsToUpdate[$insCat], $friendIdsFormatted)) {
                    $newVal = $categoryId.','.$friendIdsFormatted[$catIdsToUpdate[$insCat]]['Value'];
                    $newLinks = mt_rand(0, 2).','.$friendIdsFormatted[$catIdsToUpdate[$insCat]]['Links'];
                    $conn->executeQuery(
                        'UPDATE `Category_friends`
                        SET
                        Value = :newVal,
                        Links = :links
                        WHERE Category_ID = :cat',
                        array('newVal' => $newVal, 'cat' => $catIdsToUpdate[$insCat], 'links' => $newLinks)
                    );

                    $output->writeln($insCat);
                }
            }
            $output->writeln(sprintf('End add for %s', $categoryId));
        }

        $output->writeln('done');
    }
}
