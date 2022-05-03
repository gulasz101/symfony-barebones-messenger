<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$app = new \FrameworkX\App();

$app->get(
    '/trackings/{tracking}',
    function (\Psr\Http\Message\ServerRequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        usleep(500000);
        return \React\Http\Message\Response::json(
            [
                'tracking' => $request->getAttribute('tracking'),
                'status' => \Faker\Factory::create()->word(),
            ]
        );
    }
);

$app->run();