<?php

namespace App\Tests\Base;

class ShopTestBase extends ApiTestBase
{
    /**
     * initShopTest.
     */
    protected function initShopTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }
}
