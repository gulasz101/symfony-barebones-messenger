<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\ProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use Carbon\Carbon;
use Doctrine\DBAL\LockMode;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\UuidV4;

final class ProcessTrackingHandler implements MessageHandlerInterface
{
    use EntityManagerInterfaceAware;

    public function __invoke(ProcessTracking $message)
    {
        $tracking = new Tracking();
        $tracking->setTrackingNumber($message->getTrackingNumber());

        $this->em->persist($tracking);
        $this->em->flush();
    }
}
