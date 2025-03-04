<?php

namespace App\Dto;

use App\Entity\Cart;

final class PaymentCaptureDto
{
    public Cart $cart;
}