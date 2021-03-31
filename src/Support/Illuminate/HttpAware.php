<?php

declare(strict_types=1);

namespace App\Support\Illuminate;

use Illuminate\Http\Client\Factory as Http;

/**
 * Trait HttpAware
 * @package App\Support\Illuminate
 */
trait HttpAware
{
    private Http $http;

    /** @required */
    public function setHttp(Http $http): self
    {
        $this->http = $http;

        return $this;
    }
}
