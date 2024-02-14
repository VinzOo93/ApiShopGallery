<?php

namespace App\Validator;

use App\Validator\Constraints\CartTotal;
use App\Validator\Trait\CartValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTotalValidator extends ConstraintValidator
{
    use CartValidatorTrait;

    public function validate(mixed $total, Constraint $constraint): void
    {
        $this->initCartValidator();
        $calculatedTotal = (float) $this->cart->getSubtotal() + (float) $this->cart->getTaxes() + (float) $this->cart->getShipping();

        $this->constraint = $constraint;
        $this->checkConstraint(CartTotal::class);

        $this->checkCondition($calculatedTotal != $total);
    }
}
