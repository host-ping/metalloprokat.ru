<?php

namespace Metal\UsersBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;

class IsUniqueEmailValidator extends ConstraintValidator
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function validate($value, Constraint $constraint)
    {
        $existingUser = $this->em->getRepository('MetalUsersBundle:User')->findOneBy(array('email' => $value));
        if ($existingUser) {
            $this->context->addViolation($constraint->message);
        }
    }

} 