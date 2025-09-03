<?php

namespace App\Validator;

use App\Services\GeolocationService;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class CityCheckValidator extends ConstraintValidator
{

    private GeolocationService $geoloc;

    public function __construct(GeolocationService $geoloc)
    {
        $this->geoloc = $geoloc;
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof CityCheck) {
            throw new UnexpectedTypeException($constraint, CityCheck::class);
        }
        if (null === $value || '' === $value) {
            return;
        }
        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if ($this->geoloc->isValideCity($value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
