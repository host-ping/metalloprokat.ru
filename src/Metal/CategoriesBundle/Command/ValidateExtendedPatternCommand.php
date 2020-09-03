<?php

namespace Metal\CategoriesBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\CategoryExtended;
use Metal\CategoriesBundle\Service\ExpressionLanguage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ValidateExtendedPatternCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:categories:validate-extended-pattern');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('Start command %s at %s', $this->getName(), date('Y-m-d H:i')));
        $em = $this->getContainer()->get('doctrine');
        /* @var $em EntityManager */

        $categoriesExtended = $em
            ->getRepository('MetalCategoriesBundle:CategoryExtended')
            ->createQueryBuilder('categoryExtended')
            ->join('categoryExtended.category', 'category')
            ->where('category.allowProducts = true')
            ->getQuery()
            ->getResult()
        ;
        /* @var $categoriesExtended CategoryExtended[]  */

        $expressionLanguage = new ExpressionLanguage();
        foreach ($categoriesExtended as $categoryExtended) {
            if (!$categoryExtended->getExtendedPattern()) {
                $output->writeln(sprintf('Category "%d" not extended pattern command at %s',
                    $categoryExtended->getCategory()->getId(),
                    date('Y-m-d H:i'))
                );

                continue;
            }

            //TODO: убрать это когда появится linter https://github.com/symfony/symfony/issues/16323
            $error = '';
            set_error_handler(function($errno, $errstr) use (&$error) {
                $error = $errstr;
            });

            $expressionLanguage->compile($categoryExtended->getExtendedPattern(), array('title'));

            restore_error_handler();

            if ($error) {
                $output->writeln(
                    sprintf(
                        '%s: Category "%d" no valid pattern. Message: %s',
                        date('Y-m-d H:i'),
                        $categoryExtended->getCategory()->getId(),
                        $error
                    )
                );
            }
        }

        $output->writeln(sprintf('Done command %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
