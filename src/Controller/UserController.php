<?php

namespace App\Controller;

use App\Service\TransactionService;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $us,
        private TransactionService $ts,
    ) {
    }

    #[Route('/users', name: 'list_users', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json(
            $this->us->findAll(),
            context: ['groups' => ['user:read']]
        );
    }

    #[Route('/users', name: 'store_user', methods: ['POST'])]
    public function store(Request $req): Response
    {
        $user = $this->us->createUser($req);

        // validar o objeto usuário

        $this->us->save($user);

        return $this->json(
            [
                'message' => 'User created successfully',
                'data' => $user,
            ],
            Response::HTTP_CREATED,
            context: ['groups' => ['user:read']]
        );
    }

    #[Route('/transfer', name: 'transfer', methods: ['POST'])]
    public function transfer(Request $req): Response
    {
        $transaction = $this->ts->createTransaction($req);

        // validar o objeto transação

        $this->ts->execTransaction($transaction);

        return $this->json([
            'message' => 'Transfered successfully',
            'data' => $transaction,
        ], Response::HTTP_CREATED);
    }
}
