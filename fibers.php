<?php

require __DIR__ . "/vendor/autoload.php";

echo "START " . date('h:i:s') . PHP_EOL;

$fibers = new \Doctrine\Common\Collections\ArrayCollection();

for ($i = 0; $i < 10; $i++) {
    $f = new \Fiber(function ($f) {
        $client = new \Symfony\Component\HttpClient\CurlHttpClient();
        $r = $client
            ->request('GET', 'http://localhost:8080/trackings/lorem',
            );

        foreach ($client->stream($r) as $response => $chunk) {
            if (!$chunk->isLast()) {
                dump(date('h:i:s'), 'stop');
                $f->suspend();
            }
        }

        dump(date('h:i:s'), $r->getContent());
    });

    $f->start($f);
    $fibers->add($f);
}

do {
    $fibers = $fibers->filter(fn(\Fiber $fiber) => !$fiber->isTerminated());
    foreach ($fibers as $fiber) {
        dump(date('h:i:s'), 'start');
        $fiber->resume();
    }
} while (!$fibers->isEmpty());

echo "STOP " . date('h:i:s') . PHP_EOL;