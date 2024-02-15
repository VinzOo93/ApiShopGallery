<?php

namespace App\Validator;

use App\Entity\Cart;
use App\Validator\Constraints\CartTotal;
use App\Validator\Trait\BaseValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTotalValidator extends ConstraintValidator
{
    use BaseValidatorTrait;

    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->initValidator(Cart::class);
        $calculatedTotal = (float) $this->object->getSubtotal() + (float) $this->object->getTaxes() + (float) $this->object->getShipping();

        $this->constraint = $constraint;
        $this->checkConstraint(CartTotal::class);
        $this->checkCondition($calculatedTotal != $value);
    }
}
