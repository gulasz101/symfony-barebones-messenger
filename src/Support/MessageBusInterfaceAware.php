<?php

declare(strict_types=1);

namespace App\Support;

use Symfony\Component\Messenger\MessageBusInterface;

/**
 * Trait MessageBusInterfaceAware
 * @package App\Support
 */
trait MessageBusInterfaceAware
{
    /** @required */
    public MessageBusInterface $bus;
}
