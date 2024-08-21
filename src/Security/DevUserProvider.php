<?php

namespace App\Security;

use App\Entity\User;
use App\Service\AuthPasswordHasherService;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * DevUserProvider.
 */
class DevUserProvider implements UserProviderInterface
{
    private const array ROLE = ['ROLE_USER'];

    /**
     * __construct.
     *
     * @return void
     */
    public function __construct(
        private readonly AuthPasswordHasherService $hasher,
        private readonly string $idUserProvider,
        private readonly string $pwdUserProvider
    ) {
    }

    /**
     * loadUserByIdentifier.
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = new User();
        $user->setEmail($this->idUserProvider)
            ->setPassword(
                $this->hasher->hashPassword($this->pwdUserProvider)
            )
            ->setRoles(self::ROLE);

        return $user;
    }

    /**
     * refreshUser.
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * supportsClass.
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
