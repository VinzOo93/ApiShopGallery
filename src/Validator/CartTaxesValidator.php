<?php

namespace App\Validator;

use App\Entity\Cart;
use App\Entity\Item;
use App\Validator\Constraints\CartTaxes;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTaxesValidator extends ConstraintValidator
{
    use BaseValidatorTrait;

    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->initValidator();
        $this->constraint = $constraint;
        $this->checkConstraint(CartTaxes::class);

        if ($this->isCartInstance()) {
            /** @var Cart $cart */
            $cart = $this->object;

            $this->checkTaxValidityCart($cart);
            foreach ($this->object->getItems() as $item) {
                $this->checkTaxValidityItem($item);
                $this->checkTaxePriceItem($item);
            }
        }
    }

    /**
     * checkTaxesValidityCart.
     */
    private function checkTaxValidityCart(Cart $cart): void
    {
        $this->checkCondition($this->calculateCartTaxesBySubtotal($cart) != (float) $cart->getTaxes());
    }

    private function checkTaxValidityItem(Item $item): void
    {
        $this->checkCondition($this->calculateItemUnitPreTax($item) != (float) $item->getUnitPrice());
    }

    private function checkTaxePriceItem(Item $item): void
    {
        $this->checkCondition($this->calculaTaxePriceItem($item) != $item->getTaxPrice());
    }

    private function calculateCartTaxesBySubtotal(Cart $cart): float
    {
        return $this->calculateTaxes((float) $cart->getSubtotal());
    }

    private function calculateItemUnitPreTax(Item $item): float
    {
        return (float) $item->getUnitPreTaxPrice()
            + $this->calculateTaxes((float) $item->getUnitPreTaxPrice());
    }

    private function calculaTaxePriceItem(Item $item): float
    {
        return $this->calculatePrice($item->getUnitPreTaxPrice()) * $item->getQuantity();
    }

    /**
     * calculateTaxes.
     */
    private function calculateTaxes(float $amount): float
    {
        return $amount * self::TAXE_RATE / '100.00';
    }

    private function calculatePrice(float $amount): float
    {
        return $amount * (1 + self::TAXE_RATE / 100);
    }
}
