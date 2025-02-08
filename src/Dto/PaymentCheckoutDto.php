<?php

namespace App\Dto;

use App\Entity\Cart;
use Symfony\Component\Validator\Constraints as Assert;

final class PaymentCheckoutDto
{
    public Cart $cart;
    #[Assert\Email]
    public string $email;
    #[Assert\NotBlank]
    public string $address;
    #[Assert\NotBlank]
    public string $postalCode;
    #[Assert\NotBlank]
    public string $city;
    #[Assert\NotBlank]
    public string $country;
}