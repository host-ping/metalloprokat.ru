<?php

namespace Metal\ServicesBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;
use Metal\CompaniesBundle\Entity\Company;

class AgreementHelper extends HelperAbstract
{
    public function getMatches(Company $company)
    {
        $paymentDetails = $company->getPaymentDetails();

        $arrayMatch['company']['title'] = $company->getTitle() ?: '__________';
        $arrayMatch['company']['companyType']['title'] = $company->getCompanyType() ? $company->getCompanyType()->getTitle() : '__________';

        $arrayMatch['paymentDetails']['nameOfLegalEntity'] = $paymentDetails->getNameOfLegalEntity();
        $arrayMatch['paymentDetails']['inn'] = $paymentDetails->getInn();
        $arrayMatch['paymentDetails']['kpp'] = $paymentDetails->getKpp();
        $arrayMatch['paymentDetails']['ogrn'] = $paymentDetails->getOrgn();
        $arrayMatch['paymentDetails']['director'] = $paymentDetails->getDirectorFullName();
        $arrayMatch['paymentDetails']['mailAddress'] = $paymentDetails->getMailAddress();
        $arrayMatch['paymentDetails']['legalAddress'] = $paymentDetails->getLegalAddress();
        $arrayMatch['paymentDetails']['rs'] = $paymentDetails->getBankAccount();
        $arrayMatch['paymentDetails']['corpBill'] = $paymentDetails->getBankCorrespondentAccount();
        $arrayMatch['paymentDetails']['bik'] = $paymentDetails->getBankBik();
        $arrayMatch['paymentDetails']['bank'] = $paymentDetails->getBankTitle();

        foreach ($arrayMatch['paymentDetails'] as $key => $match) {
            if (!$match) {
                $arrayMatch['paymentDetails'][$key] = '__________';
            }
        }

        return $arrayMatch;
    }
}
