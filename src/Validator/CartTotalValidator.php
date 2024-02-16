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

    /**
     * @param mixed $value
     * @param Constraint $constraint
     * @return void
     */
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

            $this->checkCondition($this->calculateTotal($valuesTotal) != $cart->getTotal());

            foreach ($cart->getItems() as $item) {
                $pretaxPrice = (float) $this->calculatePreTaxPrice($item);
                $this->checkCondition($pretaxPrice != $item->getPreTaxPrice());
                $this->checkCondition($this->calculateTaxPrice($item) != $item->getTaxPrice());
            }
            $this->checkCondition(floatval($cart->getSubtotal()) != $pretaxPrice);
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

    /**
     * @param Item $item
     * @return float
     */
    private function calculateTaxPrice(Item $item): float
    {
        return (float) $item->getUnitPrice() * (int) $item->getQuantity();
    }

    /**
     * @param Item $item
     * @return float
     */
    private function calculatePreTaxPrice(Item $item): float
    {
        return (float) $item->getUnitPreTaxPrice() * (int) $item->getQuantity();
    }
}
