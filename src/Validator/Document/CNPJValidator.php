<?php

namespace App\Validator\Document;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CNPJValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        /* @var CNPJ $constraint */

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

    private function isFirstSecurityDigitValid(string $cnpj): bool
    {
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 12; ++$i) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit = ($remainder < 2) ? 0 : 11 - $remainder;

        return $digit === (int) $cnpj[12];
    }

    private function isSecondSecurityDigitValid(string $cnpj): bool
    {
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;

        for ($i = 0; $i < 13; ++$i) {
            $sum += (int) $cnpj[$i] * $weights[$i];
        }

        $remainder = $sum % 11;
        $digit = ($remainder < 2) ? 0 : 11 - $remainder;

        return $digit === (int) $cnpj[13];
    }

    private function followsPattern(string $cnpj): bool
    {
        return preg_match('/^\d{2}\.\d{3}\.\d{3}\/\d{4}-\d{2}$/', $cnpj);
    }
}
