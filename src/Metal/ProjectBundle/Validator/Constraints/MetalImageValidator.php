<?php

namespace Metal\ProjectBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\ImageValidator;

class MetalImageValidator extends ImageValidator
{
    /**
     * {@inheritdoc}
     */
    public function validate($value, Constraint $constraint)
    {
        $violations = count($this->context->getViolations());

        parent::validate($value, $constraint);

        $failed = count($this->context->getViolations()) !== $violations;

        if ($failed || null === $value || '' === $value) {
            return;
        }

        $image = @imagecreatefromstring(file_get_contents($value));

        if ($image === false) {
            $this->buildViolation($constraint->mimeTypesMessage)
                ->addViolation();
        }
    }
}
