<?php

namespace App\Validator;

use App\Entity\Cart;
use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;

trait BaseValidatorTrait
{
    public mixed $constraint;
    public mixed $object;

    public const string TAXE_RATE = '20.00';
    private $buildViolation;

    /**
     * initItemValidator.
     */
    protected function initValidator(): void
    {
        $this->object = $this->context->getObject();
    }

    /**
     * isCartInstance.
     */
    protected function isCartInstance(): bool
    {
        return $this->object instanceof Cart;
    }

    /**
     * checkConstraint.
     */
    protected function checkConstraint(string $classConstraint): void
    {
        if (!$this->constraint instanceof $classConstraint) {
            throw new UnexpectedTypeException($this->constraint, $classConstraint);
        }
    }

    /**
     * checkCondition.
     */
    protected function checkCondition(bool $condition): void
    {
        if ($condition) {
            $this->buildViolation = $this->context->buildViolation($this->constraint->message);
            $this->buildViolation
                ->addViolation();
        }
    }
}
