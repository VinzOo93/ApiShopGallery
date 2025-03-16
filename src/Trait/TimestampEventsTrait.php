<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

trait TimestampEventsTrait
{
    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
