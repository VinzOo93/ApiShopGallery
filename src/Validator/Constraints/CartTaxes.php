<?php

namespace App\Validator\Constraints;

use App\Validator\CartTaxesValidator;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class CartTaxes extends Constraint
{
    public string $message = "Le TVA n'est pas calculé au taux de 20% dans le panier ou la ligne.";

    public function validatedBy(): string
    {
        return CartTaxesValidator::class;
    }
}
