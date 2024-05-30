<?php

namespace App\Validator;

use App\Entity\Cart;
use App\Entity\Item;
use App\Validator\Constraints\CartTotal;
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

            /** @var array<int, float> $valuesTotal */
            $valuesTotal = [
                $cart->getSubtotal(),
                $cart->getTaxes(),
                $cart->getShipping(),
            ];

            $this->checkCondition($this->calculateTotal($valuesTotal) != (float) $cart->getTotal());

            foreach ($cart->getItems() as $item) {
                $pretaxPrice = $this->calculatePreTaxPrice($item);
                $this->checkCondition($pretaxPrice != (float) $item->getPreTaxPrice());
                $this->checkCondition($this->calculateTaxPrice($item) != (float) $item->getTaxPrice());
            }
            $this->checkCondition((float) $cart->getSubtotal() != (float) $pretaxPrice);
        }
    }

    /**
     * @param array<int, string|float> $values
     */
    private function calculateTotal(array $values): float|int
    {
        $total = 0;
        foreach ($values as $value) {
            $total += (float) $value;
        }

        return $total;
    }

    private function calculateTaxPrice(Item $item): float
    {
        return (float) $item->getUnitPrice() * (int) $item->getQuantity();
    }

    private function calculatePreTaxPrice(Item $item): float
    {
        return (float) $item->getUnitPreTaxPrice() * (int) $item->getQuantity();
    }
}
