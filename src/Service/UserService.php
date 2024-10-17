<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

// func para criar usuário e atualizar usuário(para receber as novas transações)

class UserService
{
    public const NOT_ENOUGH = -1;

    public function __construct(
        private UserRepository $repositoy,
        private EntityManagerInterface $em,
    ) {
    }

    #[IsGranted('ROLE_COMMON', statusCode: Response::HTTP_FORBIDDEN, message: 'Only common users can make transactions')]
    public function validateTransaction(User $sender, string $amount): void
    {
        if (self::NOT_ENOUGH == bccomp($sender->getBalance(), $amount, 2)) {
            throw new \JsonException('The user has insufficient balance', Response::HTTP_PAYMENT_REQUIRED);
        }
    }

    public function findById(int $id): User
    {
        $user = $this->repository->find($id);
        if (!$user) {
            throw new \JsonException('Can\'t find the user with id '.$id, Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }
}
