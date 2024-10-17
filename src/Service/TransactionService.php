<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Trait\DataPersister;
use SebastianBergmann\Diff\ConfigurationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TransactionService
{
    use DataPersister;

    public function __construct(
        private UserService $userService,
        private NotifyService $notifier,
        private HttpClientInterface $client,
        private SerializerInterface $serializer,
    ) {
    }

    public function execTransaction(Transaction $transaction): void
    {
        $sender = $this->userService->findById(3/* $transaction->getSender()->getId() */);
        $receiver = $this->userService->findById(4/* $transaction->getReceiver()->getId() */);

        $this->userService->validateTransaction($sender, $transaction->getValue());

        if (!$this->transactionHasAuthorization()) {
            throw new \JsonException('Transaction does not has authorization', Response::HTTP_FORBIDDEN);
        }

        $this->transferValue($sender, $receiver, $transaction);
        $this->notifier->sendNotification($receiver, $transaction);

        $this->persist($sender);
        $this->persist($receiver);
        $this->persist($transaction, true);
    }

    private function transactionHasAuthorization(): bool
    {
        $url = $_ENV['API_AUTHORIZATOR'] ?? null;
        if (!$url) {
            throw new ConfigurationException('`API_AUTHORIZATOR` is not set in `.env` file');
        }

        $res = $this->client->request('GET', $url)->getStatusCode();

        return Response::HTTP_OK == $res;
    }

    public function createTransaction(Request $req): Transaction
    {
        return $this->serializer->deserialize(
            $req->getContent(),
            Transaction::class,
            'json',
            ['groups' => ['transaction:write']]
        )->setCreatedAt('now', 'America/Manaus');
    }

    public function transferValue(User $sender, User $receiver, Transaction $transaction): void
    {
        $sender->setBalance(bcsub($sender->getBalance(), $transaction->getValue(), 2));
        $sender->addSentTransaction($transaction);

        $receiver->setBalance(bcadd($receiver->getBalance(), $transaction->getValue(), 2));
        $receiver->addReceivedTransaction($transaction);
    }
}
