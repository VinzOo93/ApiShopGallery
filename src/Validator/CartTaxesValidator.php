<?php

namespace App\Validator;

use App\Validator\Constraints\CartTaxes;
use App\Validator\Trait\CartValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTaxesValidator extends ConstraintValidator
{
    use CartValidatorTrait;

    const TAXE_RATE = '20.00';

    public function validate(mixed $taxes, Constraint $constraint): void
    {
        $this->initCartValidator();
        $calculatedTaxesBySubTotal = (float) $this->cart->getSubtotal() * self::TAXE_RATE / '100.00';

        $this->constraint = $constraint;
        $this->checkConstraint(CartTaxes::class);
        $this->checkCondition($calculatedTaxesBySubTotal != $taxes);
    }
}
