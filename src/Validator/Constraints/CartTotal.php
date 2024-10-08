<?php

namespace App\Validator\Constraints;

use App\Validator\CartTotalValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CartTotal extends Constraint
{
    public string $message = 'Le total du panier ou de la ligne produit sont incohérents.';

    public function validatedBy(): string
    {
        return CartTotalValidator::class;
    }
}
