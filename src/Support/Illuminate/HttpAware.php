<?php

declare(strict_types=1);

namespace App\Support\Illuminate;

use Illuminate\Http\Client\Factory;

/**
 * Trait HttpAware
 * @package App\Support\Illuminate
 */
trait HttpAware
{
    /** @required */
    public Factory $http;
}
