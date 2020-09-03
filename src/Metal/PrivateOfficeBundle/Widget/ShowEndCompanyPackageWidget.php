<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyPackage;
use Metal\ProjectBundle\Helper\FormattingHelper;

class ShowEndCompanyPackageWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    protected function setDefaultOptions()
    {
        parent::setDefaultOptions();

        $this->optionsResolver
            ->setRequired(array('company'))
            ->setAllowedTypes('company', array(Company::class))
            ->setDefaults(array(
                'start_to_show' => 7,
                'show_on_main' => false,
                'always_show' => false
            ))
        ;
    }

    public function getParametersToRender()
    {
        $company = $this->options['company'];
        /* @var $company Company */

        $companyPackage = null;
        $days = null;
        $formattingHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /** @var $formattingHelper FormattingHelper */

        if ($company->getPackageChecker()->isPaidAccount()) {
            $companyPackageRepo = $this->getDoctrine()->getRepository('MetalCompaniesBundle:CompanyPackage');
            $companyPackage = $companyPackageRepo->findOneBy(array('company' => $company->getId()));
            /* @var $package CompanyPackage */

            if (!$companyPackage) {
                throw new \LogicException(sprintf(
                    'У компании платника должен быть пакет услуг. Нужно добавить в Message106 новую запись для компании с id = %d и выставить компанию',
                    $company->getId()
                ));
            }

            $days = $formattingHelper->getDaysEnd($companyPackage->getEndAt());
        } elseif ($company->getSprosEndsAt()) {
            $days = $formattingHelper->getDaysEnd($company->getSprosEndsAt());
        }

        if ($days && $days >= 0 && $this->options['always_show']) {
            return compact('companyPackage', 'days', 'company');
        }

        if (!$this->options['show_on_main'] && ((null === $days) || ($days > $this->options['start_to_show']) || ($days < 0))) {
            return array('days' => null);
        }

        return compact('companyPackage', 'days', 'company');
    }

    public function shouldBeRendered()
    {
        $checker = $this->container->get('security.authorization_checker');

        return $checker->isGranted('ROLE_SUPPLIER') && $checker->isGranted('ROLE_APPROVED_USER');
    }
}
