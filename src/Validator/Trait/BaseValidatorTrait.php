<?php

namespace App\Validator\Trait;

use Symfony\Component\HttpFoundation\File\Exception\UnexpectedTypeException;
use UnexpectedValueException;

trait BaseValidatorTrait
{
    public mixed $constraint;
    public mixed $object;

    /**
     * initItemValidator
     *
     * @return void
     */
    protected function initValidator(string $class): void
    {
        $this->object = $this->context->getObject();
        $this->checkInstanceOfObject($class);
    }

    /**
     * checkInstanceOfObject
     *
     * @return void
     */
    private function checkInstanceOfObject($class): void
    {
        if (!$this->object instanceof $class) {
            throw new UnexpectedValueException("Expected instance of $class");
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
