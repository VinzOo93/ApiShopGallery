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
    private const EMAIL_DEV = 'dev@apiShopGallery.com';
    private const PASSWORD_DEV = 'devShop';
    private const ROLE_DEV = ['ROLE_USER'];

    private AuthPasswordHasherService $hasher;

    /**
     * __construct.
     *
     * @param AuthPasswordHasherService $hasher
     *
     * @return void
     */
    public function __construct(AuthPasswordHasherService $hasher)
    {
        $this->hasher = $hasher;
    }

    /**
     * loadUserByIdentifier.
     *
     * @param string $identifier
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = new User();
        $user->setEmail(self::EMAIL_DEV)
            ->setPassword(
                $this->hasher->hashPassword(self::PASSWORD_DEV)
            )
            ->setRoles(self::ROLE_DEV);

        return $user;
    }

    /**
     * refreshUser.
     *
     * @param UserInterface $user
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * supportsClass.
     *
     * @param string $class
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
