<?php

declare(strict_types=1);

namespace App\Support\Services;

use App\Services\TrackingResolver;

/**
 * trait TrackingResolverAware
 * @package App\Support\Services
 */
trait TrackingResolverAware
{
    /** @required */
    public TrackingResolver $trackingResolver;
}
