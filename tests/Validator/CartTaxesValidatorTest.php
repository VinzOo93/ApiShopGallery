<?php

namespace App\Tests\Validator;

use App\Entity\Cart;
use App\Entity\Item;
use App\Validator\CartTaxesValidator;
use App\Validator\Constraints\CartTaxes;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CartTaxesValidatorTest extends KernelTestCase
{
    private ConstraintValidator $constraintValidator;
    private ExecutionContextInterface $context;
    public CartTaxes $constraint;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->constraintValidator = self::getContainer()->get(CartTaxesValidator::class);
        $this->constraintValidator->initialize($this->context);
        $this->constraint = new CartTaxes();
    }

    /**
     * @throws Exception
     */
    public function testInvalidCartTaxes(): void
    {
        $cart = new Cart();
        $cart->setSubtotal(100.00);
        $cart->setTaxes(15.00);
        $cart->setShipping(10.00);
        $cart->setTotal(130.00);

        $this->checkViolations($cart);

    }

    /**
     * @throws Exception
     */
    public function testInvalidItemUnitPrice(): void
    {
        $cart = new Cart();
        $cart->setSubtotal(100.00);
        $cart->setTaxes(20.00);
        $cart->setShipping(10.00);
        $cart->setTotal(130.00);

        $item = new Item();
        $item->setUnitPreTaxPrice(50.00);
        $item->setUnitPrice(55.00);
        $item->setPreTaxPrice(100.00);
        $item->setTaxPrice(120.00);
        $item->setQuantity(2);
        $cart->addItem($item);
        $this->checkViolations($cart);

    }

    /**
     * @throws Exception
     */
    private function checkViolations(Cart $cart): void
    {
        $this->context->method('getObject')->willReturn($cart);
        $violationBuilder = $this->createMock(ConstraintViolationBuilderInterface::class);

        $violationBuilder->expects($this->once())
            ->method('addViolation');
        $this->context->expects($this->once())
            ->method('buildViolation')
            ->willReturn($violationBuilder);
        $this->constraintValidator->validate($cart, $this->constraint);
    }
}
