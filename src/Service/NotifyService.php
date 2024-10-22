<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Infrastructure\Exception\DotEnvConfigurationException;
use App\Infrastructure\Exception\NotifierNotAvalibleException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class NotifyService
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function sendNotification(User $receiver, Transaction $transaction): void
    {
        $url = $_ENV['API_NOTIFIER'] ?? null;
        if (!$url) {
            throw new DotEnvConfigurationException('Can\'t send notification. Is not possible acess to sevice that provides notification');
        }

        $data = [
            'email' => $receiver->getEmail(),
            'value' => $transaction->getValue(),
            'dateTime' => $transaction->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        // make request
        $res = $this->client->request('POST', $url, [
            'json' => $data,
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);

        $res = $this->client->request('POST', $url);
        if (Response::HTTP_GATEWAY_TIMEOUT == $res->getStatusCode()) {
            throw new NotifierNotAvalibleException('The service is not available, try again later');
        }
    }
}
