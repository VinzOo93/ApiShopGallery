<?php

namespace App\Validator\Trait;

use App\Entity\Cart;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

trait BaseValidatorTrait
{
    public mixed $constraint;
    public mixed $object;

    const TAXE_RATE = '20.00';

    /**
     * initItemValidator
     *
     * @return void
     */
    protected function initValidator(): void
    {
        $this->object = $this->context->getObject();
    }

    /**
     * isCartInstance
     *
     * @return bool
     */
    protected function isCartInstance(): bool
    {
        return $this->object instanceof Cart;
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
