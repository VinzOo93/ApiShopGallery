<?php

namespace App\Validator;

use App\Entity\Item;
use App\Validator\Constraints\CartTaxes;
use App\Validator\Trait\BaseValidatorTrait;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class CartTaxesValidator extends ConstraintValidator
{
    use BaseValidatorTrait;

    private float $unitPrice = 0;
    private Item $item;

    public function validate(mixed $value, Constraint $constraint): void
    {
        $this->initValidator();
        $this->constraint = $constraint;
        $this->checkConstraint(CartTaxes::class);
        if ($this->isCartInstance()) {
            $this->checkTaxValidityCart();
            foreach ($this->object->getItems() as $item) {
                $this->item = $item;
                $this->checkTaxValidityItem();
            }
        }
    }

    /**
     * checkTaxesValidityCart.
     */
    private function checkTaxValidityCart(): void
    {
        $this->checkCondition($this->calculateCartTaxesBySubtotal() != (float) $this->object->getTaxes());
    }

    /**
     * checkTaxesValidityItem.
     */
    private function checkTaxValidityItem(): void
    {
        $this->checkCondition($this->calculateItemUnitPreTax() != (float) $this->item->getUnitPrice());
    }

    /**
     * calculateTaxesBySubtotal.
     */
    private function calculateCartTaxesBySubtotal(): float
    {
        return $this->calculateTaxes((float) $this->object->getSubtotal());
    }

    /**
     * calculateItemTaxes.
     */
    private function calculateItemUnitPreTax(): float
    {
        return (float) $this->item->getUnitPreTaxPrice()
            + $this->calculateTaxes((float) $this->item->getUnitPreTaxPrice());
    }

    /**
     * calculateTaxes.
     */
    private function calculateTaxes(float $amount): float
    {
        return $amount * self::TAXE_RATE / '100.00';
    }
}
