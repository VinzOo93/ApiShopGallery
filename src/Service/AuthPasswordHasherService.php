<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AuthPasswordHasherService
{
    /**
     * __construct.
     *
     * @return void
     */
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    /**
     * hashPassword.
     */
    public function hashPassword(string $plainPassword): string
    {
        return $this->hasher->hashPassword(new User(), $plainPassword);
    }
}
