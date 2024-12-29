<?php

namespace App\Dto;

use App\Entity\Cart;

final class PaymentCheckoutDto
{
    public Cart $cart;
}