<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Entity\User;
use App\Infrastructure\Exception\UserDontHasPermissioException;
use App\Trait\DataPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransactionService
{
    use DataPersister;

    public function __construct(
        private UserService $userService,
        private NotifyService $notifier,
        private AuthorizationService $authService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    public function execTransaction(Transaction $transaction): void
    {
        $sender = $this->userService->findById($transaction->getSender()->getId());
        $receiver = $this->userService->findById($transaction->getReceiver()->getId());

        $this->userService->canMakeTransaction($sender, $transaction->getValue());

        if (!$this->authService->transactionHasAuthorization()) {
            throw new UserDontHasPermissioException('Transaction does not has authorization');
        }

        $this->transferValue($sender, $receiver, $transaction);
        $this->notifier->sendNotification($receiver, $transaction);

        $this->persist($sender);
        $this->persist($receiver);
        $this->persist($transaction, true);
    }

    /**
     * Creates a new transaction with the given request content.
     *
     * The request's content must be a JSON with the following structure:
     * {
     *  "sender": int,
     *  "receiver": int,
     *  "value": string
     * }
     *
     * The transaction will be created with the given data and its createdAt field set to the current time.
     *
     * @param Request $req The request containing the transaction's data in JSON format
     *
     * @return Transaction The newly created transaction
     */
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

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Transaction::class)->findAll();
    }
}
