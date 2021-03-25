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
        $this->em->getConnection()->beginTransaction();
        try {
            /** @var Tracking $tracking */
            $tracking = $this->em->createQuery(
                'SELECT t FROM App\Entity\Tracking t WHERE t.id = :id'
            )
                ->setParameter('id', $message->getTrackingNumber())
                ->setLockMode(LockMode::PESSIMISTIC_WRITE)
                ->getResult()
            ;

            if (null !== $tracking->getStatus()) {
                // this tracking was already processed.
                return;
            }

            $trackingData = $this->trackingResolver->performGetRequest($message->getTrackingNumber());
            $tracking->setStatus($trackingData['status']);
            $tracking->setDeliveryAt($trackingData['delivery_at']);
            $tracking->setRegisteredAt($trackingData['registered_at']);

            $this->em->flush();

            $this->em->getConnection()->commit();
        } catch (\Exception $e) {
            $this->em->getConnection()->rollBack();

            throw $e;
        }
    }
}
