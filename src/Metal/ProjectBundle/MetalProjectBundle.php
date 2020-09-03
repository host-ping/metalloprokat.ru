<?php

namespace Metal\ProjectBundle;

use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CompaniesBundle\Entity\PackageChecker;
use Metal\CompaniesBundle\Entity\ValueObject\CompanyServiceProvider;
use Metal\DemandsBundle\Entity\ValueObject\ConsumerTypeProvider;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Metal\ProjectBundle\DependencyInjection\CompilerPass\CreateSecondRouterCompilerPass;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\TerritorialBundle\Entity\Country;
use Metal\TerritorialBundle\Entity\ValueObject\MinisiteDomainsProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class MetalProjectBundle extends Bundle
{
    public function boot()
    {
        $projectFamily = $this->container->getParameter('project.family');
        $dictionaryDir = $this->container->getParameter('kernel.root_dir').'/config/dictionaries/'.$projectFamily;
        $measureTypes = require $dictionaryDir.'/measure-list.php';
        $additionalSiteTypes = require $dictionaryDir.'/additional-site-source-list.php';
        $companyAttributeTypes = require $dictionaryDir.'/company-attributes-list.php';
        $enabledCountriesIds = require $dictionaryDir.'/enabled-countries-list.php';
        $companyPackageTypes = require $dictionaryDir.'/company-packages-list.php';
        $sphinxRegexps = require $dictionaryDir.'/sphinx-regexps.php';
        CompanyServiceProvider::initializeCompanyServiceType($companyAttributeTypes);
        SiteSourceTypeProvider::initializeAdditionalSiteType($additionalSiteTypes);
        PackageChecker::initializePackageTypes($companyPackageTypes, $this->container->getParameter('kernel.debug'));
        ProductMeasureProvider::initialize($measureTypes);
        ConsumerTypeProvider::initialize(array('trader' => $this->container->getParameter('tokens.supplier')));
        $additionalMinisiteHostnames = $this->container->getParameter('additional_minisite_hostnames');
        MinisiteDomainsProvider::initialize($additionalMinisiteHostnames);
        Country::initializeEnabledCountriesIds($enabledCountriesIds);
        AttributeValue::initializeSphinxRegexps($sphinxRegexps);
    }

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CreateSecondRouterCompilerPass());
    }
}
