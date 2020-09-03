<?php

namespace Metal\CompaniesBundle\Command;

use Doctrine\DBAL\Connection;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Repository\SiteRepository;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateNewCompanySlugCommand extends ContainerAwareCommand
{
    const MAX_SLUG_LENGTH = 35;

    /**
     * @var DefaultHelper
     */
    protected $companiesHelper;

    /**
     * @var Connection
     */
    protected $conn;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    protected $allowProductsCount;

    protected $companyToCount = array();

    protected function configure()
    {
        $this->setName('metal:companies:generate-new-company-slug')
            ->addOption('allow-products-count', null, InputOption::VALUE_OPTIONAL, null, 1000)
            ->addOption('by-regex', null, InputOption::VALUE_NONE)
        ;
    }

    /**
     * @param $companyId
     *
     * @return int
     */
    protected function getProductsCountByCompanyId($companyId)
    {
        return $this->companyToCount[$companyId] = $this->conn->fetchColumn(
            'SELECT COUNT(*) AS _count FROM Message142 AS p WHERE p.Company_ID = :company_id AND p.Checked = :checked',
            array(
                'company_id' => $companyId,
                'checked' => Product::STATUS_CHECKED
            )
        );
    }

    /**
     * @param int $companyId
     *
     * @return bool
     */
    protected function isAvailableProductsCount($companyId)
    {
        return $this->getProductsCountByCompanyId($companyId) < $this->allowProductsCount;
    }

    protected function processCompany($company)
    {
        if ($this->input->getOption('by-regex')) {
            if (strlen($company['company_old_slug']) > 1 && !preg_match('/^'.Company::SLUG_REGEX.'$/ui', $company['company_old_slug'])) {
                $this->refreshSlug($company);
            }

            return null;
        }

        if ($this->isAvailableProductsCount($company['id'])) {
            $this->refreshSlug($company);
            return null;
        } else {
            $this->output->writeln(sprintf('<error>Current company <comment> %s </comment> many products</error>: count <info>%s</info>', $company['id'], $this->companyToCount[$company['id']]));
        }
    }

    protected function refreshSlug($company)
    {
        $slug = $this->companiesHelper->generateCompanySlug($company['id'], $company['title'], $company['city_slug']);

        $this->output->writeln(sprintf('<info>Generate Slug</info>: <comment>%s</comment> for company %d', $slug, $company['id']));

        $this->output->writeln(sprintf(
                '<info>INSERT company old slug table:</info> old_slug: <comment>"%s"</comment> new_slug: <comment>"%s"</comment> for company %d',
                $company['company_old_slug'],
                $slug,
                $company['id']
            )
        );

        $this->conn->executeUpdate('INSERT INTO company_old_slug (company_id, old_slug) VALUES (:company_id, :old_slug) ON DUPLICATE KEY UPDATE old_slug = :old_slug',
            array(
                'old_slug' => $company['company_old_slug'],
                'company_id' => $company['id']
            )
        );

        $this->conn->executeUpdate('UPDATE Message75 SET slug = :slug, slug_changed_at = null WHERE Message_ID = :id',
            array(
                'slug' => $slug,
                'id' => $company['id']
            )
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln(sprintf('<info>%s: Start command</info> "%s"', date('d.m.Y H:i:s'), $this->getName()));

        $this->companiesHelper = $this->getContainer()->get('brouzie.helper_factory')->get('MetalCompaniesBundle');

        $em = $this->getContainer()->get('doctrine');
        $siteRepository =  $em->getRepository('MetalProjectBundle:Site');
        /* @var $siteRepository SiteRepository */

        $companyRepository =  $em->getRepository('MetalCompaniesBundle:Company');

        $siteRepository->disableLogging();

        $this->conn = $em->getConnection();

        $this->input = $input;
        $this->output = $output;

        $this->allowProductsCount = $input->getOption('allow-products-count');

        $this->conn->getConfiguration()->setSQLLogger(null);

        $qb = $this->conn->createQueryBuilder()
            ->select('company.Message_ID AS id')
            ->addSelect('company.title AS title')
            ->addSelect('city.Keyword AS city_slug')
            ->addSelect('country.base_host AS host')
            ->addSelect('company.slug AS company_old_slug')
            ->from('Message75', 'company')
            ->join('company', 'Classificator_Country', 'country', 'company.country_id = country.Country_ID')
            ->join('country', 'Classificator_Region', 'city', 'company.company_city = city.Region_ID')
            ->where('company.deleted_at_ts = 0')
            ->orderBy('company.Message_ID')
        ;
        
        if (!$input->getOption('by-regex')) {
            $qb->andWhere('LENGTH(company.slug) > :max_length')
                ->setParameter('max_length', self::MAX_SLUG_LENGTH)
            ;
        }

        $companies = $qb->execute()->fetchAll();

        foreach ($companies as $key => $company) {
            $this->processCompany($company);

            unset($companies[$key], $this->companyToCount[$company['id']]);
        }

        $output->writeln('<info>Refresh company domain.</info>');

        $affectedRows = $companyRepository->updateCompanyDomain();

        $output->writeln(sprintf('<info>Done refresh company domain</info> affected rows: <comment>%d</comment> at %s', $affectedRows, date('Y-m-d H:i')));

        $siteRepository->restoreLogging();

        $output->writeln(sprintf('<info>End command</info> %s at %s', $this->getName(), date('Y-m-d H:i')));
    }
}
