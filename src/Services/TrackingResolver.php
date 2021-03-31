<?php

declare(strict_types=1);

namespace App\Services;

use App\Support\Illuminate\HttpAware;

/**
 * Class TrackingResolver
 * @package App\Services
 */
class TrackingResolver
{
    use HttpAware;

    private string $apiUri;

    public function __construct(string $apiUri)
    {
        $this->apiUri = $apiUri;
    }

    /**
     * @param string $trackingNumber
     * @return array
     * @throws \Illuminate\Http\Client\RequestException
     */
    public function performGetRequest(string $trackingNumber): array
    {
        return $this->http
            ->timeout(5)
            ->acceptJson()
            ->get($this->apiUri . '/trackings/' . $trackingNumber)
            ->throw()
            ->json();
    }
}
