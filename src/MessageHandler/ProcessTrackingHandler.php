<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\ProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use Carbon\Carbon;
use Doctrine\DBAL\LockMode;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\UuidV4;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ProcessTrackingHandler implements MessageHandlerInterface
{
    use EntityManagerInterfaceAware;

    public function __construct(
        private HttpClientInterface $httpClient
    )
    {
        //...
    }

    public function __invoke(ProcessTracking $message)
    {
        $this->httpClient->request(Request::METHOD_GET, 'http://localhost:8080/trackings/' . $message->getTrackingNumber());

        $tracking = new Tracking();
        $tracking
            ->setTrackingNumber($message->getTrackingNumber())
            ->setOriginMessage($message)
        ;

        $this->em->persist($tracking);
        $this->em->flush();
    }
}
