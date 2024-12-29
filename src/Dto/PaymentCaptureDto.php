<?php

namespace App\Dto;

use App\Entity\Payment;

final class PaymentCaptureDto
{
    public Payment $payment;
}