<?php

namespace App\Validator\Trait;

use App\Entity\Cart;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use UnexpectedValueException;

trait CartValidatorTrait
{
    public Cart $cart;
    public mixed $constraint;

    /**
     * initCartValidator
     *
     * @return void
     */
    protected function initCartValidator(): void
    {
        $this->cart = $this->context->getObject();
        $this->checkInstanceOfObject();
    }

    /**
     * checkInstanceOfObject
     *
     * @return void
     */
    private function checkInstanceOfObject(): void
    {
        if (!$this->cart instanceof Cart) {
            throw new UnexpectedValueException('Expected instance of Cart');
        }
    }

    /**
     * checkConstraint
     *
     * @param  string $classConstraint
     * @return void
     */
    protected function checkConstraint(string $classConstraint): void
    {
        if (!$this->constraint instanceof $classConstraint) {
            throw new UnexpectedTypeException($this->constraint, $classConstraint);
        }
    }

    /**
     * checkCondition
     *
     * @param  bool $condition
     * @return void
     */
    protected function checkCondition(bool $condition): void
    {
        if ($condition) {
            $this->context->buildViolation($this->constraint->message)
                ->addViolation();
        }
    }
}
