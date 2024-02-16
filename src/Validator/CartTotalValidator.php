<?php

namespace App\Validator;

use App\Entity\Cart;
use App\Entity\Item;
use App\Validator\Constraints\CartTotal;
use App\Validator\Trait\BaseValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTotalValidator extends ConstraintValidator
{
    use BaseValidatorTrait;

    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->initValidator();
        $this->constraint = $constraint;
        $this->checkConstraint(CartTotal::class);

        if ($this->isCartInstance()) {
            /** @var Cart $cart */
            $cart = $this->object;

            $pretaxPrice = 0;
            $valuesTotal = [
                $cart->getSubtotal(),
                $cart->getTaxes(),
                $cart->getShipping(),
            ];

            $this->checkCondition($this->calculateTotal($valuesTotal) != $cart->getTotal());

            foreach ($cart->getItems() as $item) {
                $pretaxPrice = (float) $this->calculatePreTaxPrice($item);
                $this->checkCondition($pretaxPrice != $item->getPreTaxPrice());
                $this->checkCondition($this->calculateTaxPrice($item) != $item->getTaxPrice());
            }
            $this->checkCondition(floatval($cart->getSubtotal()) != $pretaxPrice);
        }
    }

    private function calculateTotal(array $values): float|int
    {
        $total = 0;
        foreach ($values as $value) {
            $total += (float) $value;
        }
        return $total;
    }

    private function calculateTaxPrice(Item $item): float|int
    {
        return $item->getUnitPrice() * $item->getQuantity();
    }

    private function calculatePreTaxPrice(Item $item): float|int
    {
        return $item->getUnitPreTaxPrice() * $item->getQuantity();
    }
}
