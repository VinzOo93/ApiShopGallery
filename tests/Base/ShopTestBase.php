<?php

namespace App\Tests\Base;

class ShopTestBase extends ApiTestBase
{
    /**
     * initShopTest
     *
     * @return void
     */
    protected function initShopTest(): void
    {
        $this->initApiTest();
        $this->initApiEntityUserTest();
    }
}
