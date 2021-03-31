<?php
declare(strict_types=1);

namespace App\Message;

use App\Entity\Tracking;

final class ProcessTracking
{
     public string $trackingNumber;

     public function __construct(Tracking $tracking)
     {
         $this->trackingNumber = (string)$tracking->getTrackingNumber()->toRfc4122();
     }

    public function getTrackingNumber(): string
    {
        return $this->trackingNumber;
    }
}
