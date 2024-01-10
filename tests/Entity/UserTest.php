<?php

namespace App\Tests\Entity;

use App\Entity\User;
use App\Tests\Base\TestBase;

/**
 * UserTest
 */
class UserTest extends TestBase
{
    private User $user;


    protected function getContainerUser(): void
    {
        $this->getContainer();
        $this->user = new User();
    }

    /**
     * testUserSetUp
     *
     * @return void
     */
    public function testUserSetUp(): void
    {
        $this->getContainerUser();

        $this->testEmail();
        $this->testUserIdentifier();
        $this->testRole();
        $this->testPassword();
        $this->testErase();
    }

    /**
     * @return void
     */
    private function testEmail(): void
    {
        $this->user->setEmail('pablo@live.fr');
        $this->assertEquals('pablo@live.fr', $this->user->getEmail());
    }


    private function testUserIdentifier(): void
    {
        $this->assertEquals('pablo@live.fr', $this->user->getUserIdentifier());
    }

    /**
     * @return void
     */
    private function testRole(): void
    {
        $this->user->setRoles(['ROLE_USER']);
        $this->assertEquals(['ROLE_USER'], $this->user->getRoles());
    }

    /**
     * @return void
     */
    private function testPassword(): void
    {
        $this->user->setPassword('pa$$word');
        $this->assertEquals('pa$$word', $this->user->getPassword());
    }

    /**
     * testErase
     *
     * @return void
     */
    private function testErase(): void
    {
        $this->user->eraseCredentials();
        $this->assertEquals(null, $this->user->getEmail());
    }
}
