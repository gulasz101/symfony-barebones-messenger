<?php

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\BatchableProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BatchableProcessTrackingHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;
    use EntityManagerInterfaceAware;

    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
        //...
    }

    public function __invoke(BatchableProcessTracking $message, Acknowledger $ack)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        $asyncJobs = $this->initAsyncJobs($jobs);

        do {
            $asyncJobs = $asyncJobs->filter(fn(\Fiber $asyncJob) => $asyncJob->isRunning());
        } while (!$asyncJobs->isEmpty());

        $this->em->flush();
    }

    private function processSingleMessage(BatchableProcessTracking $message, Acknowledger $ack): void
    {
        try {
            $this->httpClient->request(Request::METHOD_GET, 'http://localhost:8080/trackings/' . $message->getTrackingNumber());

            $tracking = new Tracking();
            $tracking->setTrackingNumber($message->getTrackingNumber());

            $this->em->persist($tracking);

            $ack->ack();

        } catch (\Throwable $e) {
            $ack->nack($e);
        }
    }

    private function initAsyncJobs(array $jobs): ArrayCollection
    {
        $asyncJobs = new ArrayCollection();
        /**
         * @var BatchableProcessTracking $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {
            $asyncJob = new \Fiber(fn() => $this->processSingleMessage($message, $ack));

            $asyncJob->start();
            $asyncJobs->add($asyncJob);
        }

        return $asyncJobs;
    }
}
