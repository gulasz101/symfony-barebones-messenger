<?php

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\BatchableFiberProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class BatchableFiberProcessTrackingHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;
    use EntityManagerInterfaceAware;

    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
        //...
    }

    public function __invoke(BatchableFiberProcessTracking $message, Acknowledger $ack)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        $asyncJobs = $this->initAsyncJobs($jobs);

        do {
            $asyncJobs = $asyncJobs->filter(fn(\Fiber $asyncJob) => !$asyncJob->isTerminated());
            foreach ($asyncJobs as $asyncJob) {
                $asyncJob->resume();
            }
        } while (!$asyncJobs->isEmpty());

        $this->em->flush();
    }

    private function processSingleMessage(BatchableFiberProcessTracking $message, Acknowledger $ack): void
    {
        \Fiber::suspend();

        try {
            $response = $this->httpClient
                ->request(
                    Request::METHOD_GET,
                    'http://localhost:8080/trackings/' . $message->getTrackingNumber()
                )
            ;

            foreach ($this->httpClient->stream($response) as $r => $chunk) {
                if (!$chunk->isLast()) {
                    \Fiber::suspend();
                }
            }

            $tracking = new Tracking();
            $tracking
                ->setTrackingNumber($message->getTrackingNumber())
                ->setOriginMessage($message)
            ;

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
         * @var BatchableFiberProcessTracking $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {
            $asyncJob = new \Fiber(fn() => $this->processSingleMessage($message, $ack));

            $asyncJobs->add($asyncJob);
            $asyncJob->start();
        }

        return $asyncJobs;
    }
}
