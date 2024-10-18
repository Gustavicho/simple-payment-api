<?php

namespace App\Validator\Document;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CPFValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var CPF $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        if (!$this->followsPattern($value)) {
            $this->context->buildViolation($constraint->patternMessage)
                ->setParameter('{{ value }}', $value)
                ->addViolation();

            return;
        }

        // remove all non-digit characters
        // because the method don't handdles dots and etc
        $value = preg_replace('/\D/', '', $value);
        if (!$this->isFirstSecurityDigitValid($value) || !$this->isSecondSecurityDigitValid($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }

    private function isFirstSecurityDigitValid(string $cpf): bool
    {
        $sum = 0;
        for ($i = 0; $i < 9; ++$i) {
            $sum += (int) $cpf[$i] * (10 - $i);
        }

        $remainder = $sum % 11;
        $digit = ($remainder < 2) ? 0 : 11 - $remainder;

        return $digit === (int) $cpf[9];
    }

    private function isSecondSecurityDigitValid(string $cpf): bool
    {
        $sum = 0;
        for ($i = 0; $i < 10; ++$i) {
            $sum += (int) $cpf[$i] * (11 - $i);
        }

        $remainder = $sum % 11;
        $digit = ($remainder < 2) ? 0 : 11 - $remainder;

        return $digit === (int) $cpf[10];
    }

    private function followsPattern(string $cpf): bool
    {
        return preg_match('/^\d{3}\.\d{3}\.\d{3}-\d{2}$/', $cpf);
    }
}
