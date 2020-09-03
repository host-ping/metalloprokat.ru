<?php

namespace Metal\CompaniesBundle\Validator\Constraints;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Helper\DefaultHelper;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CompanySlugValidator extends ConstraintValidator
{
    /**
     * @var DefaultHelper
     */
    private $companiesHelper;

    public function __construct(HelperFactory $helperFactory)
    {
        $this->companiesHelper = $helperFactory->get('MetalCompaniesBundle');
    }

    public function validate($value, Constraint $constraint)
    {
        if (!preg_match('/^'.Company::SLUG_REGEX.'$/ui', $value)) {
            $this->context->addViolation($constraint->messageSlugNotValid);
            return;
        }

        $isAvailable = $this->companiesHelper->isSlugAvailable($value, $this->context->getRoot()->getData()->getId());
        if (!$isAvailable) {
            $this->context->addViolation($constraint->messageSlugUsed);
        }
    }
}
