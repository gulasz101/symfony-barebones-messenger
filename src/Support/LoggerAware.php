<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Log\LoggerInterface;

/**
 * trait LoggerAware
 * @package App\Support
 */
trait LoggerAware
{
    private LoggerInterface $logger;

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
