<?php

namespace Metal\CompaniesBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class CompanySlug extends Constraint
{
    public $messageSlugUsed = 'Этот адрес уже используется.';
    public $messageSlugNotValid = 'Можно использовать латинские буквы, цифры и дефис.';

    public function validatedBy()
    {
        return 'slug_validator';
    }
}
