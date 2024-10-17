<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use SebastianBergmann\Diff\ConfigurationException;
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
            throw new ConfigurationException('`API_NOTIFIER` is not set in `.env` file');
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
            throw new \JsonException('The service is not available, try again later', Response::HTTP_GATEWAY_TIMEOUT);
        }
    }
}
