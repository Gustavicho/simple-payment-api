<?php

namespace App\Service;

use App\Entity\User;
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

    #[IsGranted('ROLE_COMMON', statusCode: Response::HTTP_FORBIDDEN, message: 'Only common users can make transactions')]
    public function validateTransaction(User $sender, string $amount): void
    {
        if (self::NOT_ENOUGH == bccomp($sender->getBalance(), $amount, 2)) {
            throw new \JsonException('The user has insufficient balance', Response::HTTP_PAYMENT_REQUIRED);
        }
    }

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
            throw new \JsonException('The id is required', Response::HTTP_BAD_REQUEST);
        }

        $user = $this->repository->find($id);
        if (!$user) {
            throw new \JsonException('Can\'t find the user with id '.$id, Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
