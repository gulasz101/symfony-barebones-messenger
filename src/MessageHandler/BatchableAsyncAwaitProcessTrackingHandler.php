<?php
declare(strict_types=1);

namespace App\MessageHandler;

use App\Entity\Tracking;
use App\Message\BatchableAsyncAwaitProcessTracking;
use App\Support\EntityManagerInterfaceAware;
use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use React\EventLoop\Loop;
use React\Http\Browser;
use React\Promise\Promise;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\Handler\Acknowledger;
use Symfony\Component\Messenger\Handler\BatchHandlerInterface;
use Symfony\Component\Messenger\Handler\BatchHandlerTrait;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use function React\Async\async;
use function React\Async\await;
use function React\Async\parallel;
use function React\Promise\all;

final class BatchableAsyncAwaitProcessTrackingHandler implements BatchHandlerInterface
{
    use BatchHandlerTrait;
    use EntityManagerInterfaceAware;

    public function __invoke(BatchableAsyncAwaitProcessTracking $message, Acknowledger $ack)
    {
        return $this->handle($message, $ack);
    }

    private function process(array $jobs): void
    {
        $trackings = new ArrayCollection();
        /**
         * @var BatchableAsyncAwaitProcessTracking $message
         * @var Acknowledger $ack
         */
        foreach ($jobs as [$message, $ack]) {
            async(function () use ($message, $ack) {
                await((new Browser())
                    ->get('http://localhost:8080/trackings/' . $message->getTrackingNumber())
                );

                return await(new Promise(function () use ($message, $ack) {
                        $tracking = new Tracking();
                        $tracking
                            ->setTrackingNumber($message->getTrackingNumber())
                            ->setOriginMessage($message);

                        $this->em->persist($tracking);

                    return [$tracking, $ack];
                }));
            })()->then(function () use ($trackings) {
                [$tracking, $ack] = func_get_args();
                $trackings->add($tracking);
                $ack->ack();
            });
        }

        $panicTimeout = Carbon::now();
        while ($trackings->count() !== count($jobs)) {
            if ($panicTimeout->diff(Carbon::now())->s > 60) {
                throw new \RuntimeException('Timeout reached.');
            }
        }

        $this->em->flush();
    }
}
