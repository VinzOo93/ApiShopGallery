<?php

namespace App\Validator;

use App\Entity\Cart;
use App\Validator\Constraints\CartTaxes;
use App\Validator\Trait\BaseValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTaxesValidator extends ConstraintValidator
{
    use BaseValidatorTrait;

    public function validate(mixed $taxes, Constraint $constraint): void
    {
        $this->initValidator(Cart::class);
        $calculatedTaxesBySubTotal = (float) $this->object->getSubtotal() * self::TAXE_RATE / '100.00';

        $this->constraint = $constraint;
        $this->checkConstraint(CartTaxes::class);
        $this->checkCondition($calculatedTaxesBySubTotal != $taxes);
    }
}
