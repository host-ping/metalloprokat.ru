<?php

namespace Metal\UsersBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsUniqueEmail extends Constraint
{
    public $message = 'Пользователь с таким email уже существует.';

    public function validatedBy()
    {
        return 'useremail_validator';
    }
}