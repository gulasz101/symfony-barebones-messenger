<?php

namespace App\MessageHandler;

use App\Message\EmptyMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class EmptyMessageHandler implements MessageHandlerInterface
{
    public function __invoke(EmptyMessage $message)
    {
        dump($message->getName());
        sleep(1);
    }
}
