<?php

namespace App\Service;

use App\Entity\User;
use App\Infrastructure\Exception\CantFindUserException;
use App\Infrastructure\Exception\UserInsuficientBalanceException;
use App\Repository\UserRepository;
use App\Trait\DataPersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

// func para criar usuário e atualizar usuário(para receber as novas transações)

class UserService
{
    use DataPersister;

    public const NOT_ENOUGH = -1;

    public function __construct(
        private UserRepository $repository,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private EntityManagerInterface $entityManager,
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * Checks if the given user can make a transaction with the given amount.
     *
     * The user can make the transaction if and only if the user's balance is
     * greater than or equal to the given amount.
     *
     * Only common users can make transactions.
     *
     * @param User   $sender the user trying to make the transaction
     * @param string $amount the amount of the transaction
     *
     * @throws UserInsuficientBalanceException if the user's balance is insufficient
     */
    #[IsGranted('ROLE_COMMON', statusCode: Response::HTTP_FORBIDDEN, message: 'Only common users can make transactions')]
    public function canMakeTransaction(User $sender, string $amount): void
    {
        if (self::NOT_ENOUGH == bccomp($sender->getBalance(), $amount, 2)) {
            throw new UserInsuficientBalanceException('The user has insufficient balance');
        }
    }

    /**
     * Creates a new user with the given request content.
     *
     * The request's content must be a JSON with the following structure:
     * {
     *  "email": string,
     *  "fullName": string,
     *  "password": string,
     *  "document": string,
     *  "balance": string
     * }
     *
     * The user will be created with the given data and its createdAt field set to the current time.
     *
     * @param Request $req The request containing the user's data in JSON format
     *
     * @return User The newly created user
     */
    public function createUser(Request $req): User
    {
        return $this->serializer->deserialize(
            $req->getContent(),
            User::class,
            'json',
            ['groups' => ['user:write']]
        )->setCreatedAt('now', 'America/Manaus');
    }

    public function findById(?int $id): User
    {
        if (!$id) {
            throw new CantFindUserException('The id is required', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->find($id);
        if (!$user) {
            throw new CantFindUserException('Can\'t find the user with id '.$id);
        }

        return $user;
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
