<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\ProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use App\Support\Services\TrackingResolverAware;
use Doctrine\DBAL\LockMode;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class ProcessTrackingHandler implements MessageHandlerInterface
{
    use EntityManagerInterfaceAware;
    use TrackingResolverAware;

    public function __invoke(ProcessTracking $message)
    {
        dump(sprintf('Attempting #%s', $message->getTrackingNumber()));
        $this->em->getConnection()->beginTransaction();
        try {
            /** @var Tracking[] $trackings */
            $trackings = $this->em->getRepository(Tracking::class)
                ->createQueryBuilder('t')
                ->where('t.id = :id')
                ->setParameter('id', (int)$message->getTrackingNumber())
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
                $tracking->setDeliveryAt($trackingData['delivery_at']);
                $tracking->setRegisteredAt($trackingData['registered_at']);

                $this->em->flush();
            }

            $this->em->getConnection()->commit();

            dump(sprintf('Tracking #%s processed', $tracking->getId()));
        } catch (\Throwable $e) {
            $this->em->getConnection()->rollBack();
            dump($e);

            throw $e;
        }
    }
}
