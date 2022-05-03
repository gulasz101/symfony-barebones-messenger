<?php

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\BatchableProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class BatchableProcessTrackingHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;
    use EntityManagerInterfaceAware;

    public function __invoke(BatchableProcessTracking $message, Acknowledger $ack)
    {
        return $this->handle($message, $ack);
    }

    /**
     * @list<array{0: object, 1: Acknowledger}> $jobs
     */
    private function process(array $jobs): void
    {
        /**
         * @var BatchableProcessTracking $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {

            try {
                $tracking = new Tracking();
                $tracking->setTrackingNumber($message->getTrackingNumber());

                $this->em->persist($tracking);

                $ack->ack();

            } catch (\Throwable $e) {
                $ack->nack($e);
            }

            $this->em->flush();
        }
    }
}