<?php

namespace App\Message;

use Symfony\Component\Uid\Uuid;

final class BatchableProcessTracking
{
    private string $trackingNumber;

    public function __construct()
    {
        $this->trackingNumber = (string)Uuid::v6()->toRfc4122();
    }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }
}
