<?php

namespace Metal\ContentBundle\Command;

use Doctrine\ORM\EntityManager;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Metal\ContentBundle\Entity\ParserCompanyToCategory;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateParsedCompaniesCategoryCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('metal:content:update-parsed-companies-category');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('%s: Start command "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $em = $this->getContainer()->get('doctrine')->getManager();
        /** @var $em EntityManager */

        $parserCompanyToCategories = $em->createQueryBuilder()
            ->select('parserCompanyToCategory')
            ->addSelect('company')
            ->addSelect('parsedCategory')
            ->addSelect('category')
            ->from('MetalContentBundle:ParserCompanyToCategory', 'parserCompanyToCategory')
            ->join('parserCompanyToCategory.company', 'company')
            ->join('parserCompanyToCategory.parsedCategory', 'parsedCategory')
            ->join('parsedCategory.category', 'category')
            ->where('parserCompanyToCategory.matched = false')
            ->addOrderBy('parserCompanyToCategory.company', 'DESC')
            ->getQuery()
            ->getResult()
        ;
        /* @var $parserCompanyToCategories ParserCompanyToCategory[] */

        $output->writeln(sprintf('%s: Found %d parsed categories', date('d.m.Y H:i:s'), count($parserCompanyToCategories)));

        foreach ($parserCompanyToCategories as $key => $parserCompanyToCategory) {
            $output->writeln(sprintf('%s: Add new CompanyCategory for company: "%s"', 
                date('d.m.Y H:i:s'), 
                $parserCompanyToCategory->getCompany()->getTitle())
            );
            
            $companyCategory = new CompanyCategory();
            $companyCategory->setCategory($parserCompanyToCategory->getParsedCategory()->getCategory());

            $output->writeln(
                sprintf('%s: ParsedCategory title: "%s" Category title: "%s"',
                    date('d.m.Y H:i:s'),
                    $parserCompanyToCategory->getParsedCategory()->getTitle(),
                    $parserCompanyToCategory->getParsedCategory()->getCategory()->getTitle()
                )
            );
            
            $parserCompanyToCategory->getCompany()->addCompanyCategory($companyCategory);
            $parserCompanyToCategory->setMatched(true);
            
            $em->flush();

            unset($parserCompanyToCategories[$key]);
        }

        $output->writeln(sprintf('%s: Done command "%s"', date('d.m.Y H:i:s'), $this->getName()));
    }

}
