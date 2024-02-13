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
    public function validate(mixed $cart, Constraint $constraint): void
    {
        if (!$constraint instanceof CartTotal) {
            throw new UnexpectedTypeException($constraint, CartTotal::class);
        }

        if (!$cart instanceof Cart) {
            throw new UnexpectedValueException($cart, Cart::class);
        }
        $isValid = true;
        if (!$isValid) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
