<?php

namespace App\Tests\Validator;

use App\Entity\Cart;
use App\Entity\Item;
use App\Validator\CartTotalValidator;
use App\Validator\Constraints\CartTotal;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

class CartTotalValidatorTest extends KernelTestCase
{
    private CartTotalValidator $constraintValidator;
    private ExecutionContextInterface $context;
    private CartTotal $constraint;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->constraintValidator = self::getContainer()->get(CartTotalValidator::class);
        $this->constraintValidator->initialize($this->context);
        $this->constraint = new CartTotal();
    }

    /**
     * @throws Exception
     */
    public function testInvalidCartTotal()
    {
        $cart = new Cart();
        $cart->setSubtotal(50.00);
        $cart->setTaxes(10.00);
        $cart->setShipping(10.00);
        $cart->setTotal(60.00);

        $item = new Item();
        $item->setUnitPreTaxPrice(50.00);
        $item->setUnitPrice(60.00);
        $item->setPreTaxPrice(50.00);
        $item->setTaxPrice(60.00);
        $item->setQuantity(1);
        $cart->addItem($item);

        $this->checkViolations($cart);
    }

    /**
     * @throws Exception
     */
    public function testInvalidItemPreTaxPrice()
    {
        $cart = new Cart();
        $cart->setSubtotal(100.00);
        $cart->setTaxes(20.00);
        $cart->setShipping(10.00);
        $cart->setTotal(130.00);

        $item = new Item();
        $item->setUnitPreTaxPrice(50.00);
        $item->setUnitPrice(60.00);
        $item->setPreTaxPrice(40.00);
        $item->setTaxPrice(120.00);
        $item->setQuantity(2);
        $cart->addItem($item);

        $this->checkViolations($cart);
    }

    /**
     * @throws Exception
     */
    public function testInvalidSubtotal()
    {
        $cart = new Cart();
        $cart->setSubtotal(120.00);
        $cart->setTaxes(20.00);
        $cart->setShipping(10.00);
        $cart->setTotal(150.00);

        $item = new Item();
        $item->setUnitPreTaxPrice(50.00);
        $item->setUnitPrice(60.00);
        $item->setPreTaxPrice(100.00);
        $item->setTaxPrice(120.00);
        $item->setQuantity(2);
        $item->setCart($cart);
        $this->checkViolations($cart);
    }

    /**
     * @throws Exception
     */
    public function checkViolations(Cart $cart): void
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
