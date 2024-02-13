<?php

namespace App\Validator\Constraints;

use App\Validator\CartValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CartTotal extends Constraint
{
    public string $message = 'Le total du panier ne correspond pas à la somme des articles avec la TVA.';

    public function validatedBy(): string
    {
        return CartValidator::class;
    }
}
