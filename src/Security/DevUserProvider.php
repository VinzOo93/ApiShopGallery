<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * DevUserProvider
 */
class DevUserProvider implements UserProviderInterface
{

    /**
     * loadUserByIdentifier
     *
     * @param  mixed $identifier
     * @return UserInterface
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Retourner un utilisateur "en dur" pour le développement
        return new User('dev@apiShopGallery.com', 'devShop', ['ROLE_USER']);
    }

    /**
     * refreshUser
     *
     * @param  mixed $user
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        return $user;
    }

    /**
     * supportsClass
     *
     * @param  mixed $class
     * @return bool
     */
    public function supportsClass(string $class): bool
    {
        return User::class === $class;
    }
}
