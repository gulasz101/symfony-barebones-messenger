<?php
declare(strict_types=1);

namespace App\Message;

use Symfony\Component\Uid\Uuid;

final class ProcessTracking
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
