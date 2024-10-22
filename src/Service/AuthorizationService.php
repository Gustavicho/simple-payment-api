<?php

namespace App\Service;

use App\Infrastructure\Exception\DotEnvConfigurationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class AuthorizationService
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function transactionHasAuthorization(): bool
    {
        $url = $_ENV['API_AUTHORIZATOR'] ?? null;
        if (!$url) {
            throw new DotEnvConfigurationException('Is not possible acess to sevice that provides authorization');
        }

        $res = $this->client->request('GET', $url)->getStatusCode();

        return Response::HTTP_OK == $res;
    }
}
