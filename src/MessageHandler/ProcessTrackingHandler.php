<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\ProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use App\Support\LoggerAware;
use App\Support\Services\TrackingResolverAware;
use Carbon\Carbon;
use Doctrine\DBAL\LockMode;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Uid\UuidV4;

final class ProcessTrackingHandler implements MessageHandlerInterface
{
    use LoggerAware;
    use EntityManagerInterfaceAware;
    use TrackingResolverAware;

    public function __invoke(ProcessTracking $message)
    {
        $this->em->getConnection()->beginTransaction();
        try {
            /** @var Tracking[] $trackings */
            $trackings = $this->em->getRepository(Tracking::class)
                ->createQueryBuilder('t')
                ->where('t.tracking_number = :tracking_number')
                ->setParameter('tracking_number', UuidV4::fromString($message->getTrackingNumber()), 'uuid')
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)
                ->getResult();

            foreach ($trackings as $tracking) {
                if (null !== $tracking->getStatus()) {
                    // this tracking was already processed.
                    $this->em->getConnection()->commit();
                    return;
                }

                $trackingData = $this->trackingResolver->performGetRequest($message->getTrackingNumber());
                $tracking->setStatus($trackingData['status']);
                $tracking->setDeliveryAt(
                    Carbon::make($trackingData['delivery_at'])
                );
                $tracking->setRegisteredAt(
                    Carbon::make($trackingData['registered_at'])
                );

                $this->em->flush();
                $this->logger->info(sprintf('#%s processed with with: %s', $tracking->getId(), $message->getTrackingNumber()));
            }

            $this->em->getConnection()->commit();

        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();

            $this->logger->error($e->getMessage(), $e->getTrace());

            throw $e;
        }
    }
}
