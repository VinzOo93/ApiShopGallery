<?php

namespace App\Validator;

use App\Entity\Cart;
use App\Validator\Constraints\CartTotal;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use UnexpectedValueException;

class CartValidator extends ConstraintValidator
{
    public function validate(mixed $total, Constraint $constraint): void
    {

        /** @var Cart */
        $cart = $this->context->getObject();
        $calculatedTotal = $cart->getSubtotal() + $cart->getTaxes() + $cart->getShipping();

        if (!$cart instanceof Cart) {
            throw new UnexpectedValueException('Expected instance of Cart');
        }

        if (!$constraint instanceof CartTotal) {
            throw new UnexpectedTypeException($constraint, CartTotal::class);
        }
        if ($calculatedTotal != $cart->getTotal()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
