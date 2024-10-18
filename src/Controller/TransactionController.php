<?php

namespace App\Controller;

use App\Service\TransactionService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class TransactionController extends AbstractController
{
    public function __construct(
        private TransactionService $transactionService,
    ) {
    }

    #[Route('/transactions', name: 'list_transactions', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json(
            $this->transactionService->findAll(),
            context: ['groups' => ['transaction:read']]
        );
    }


    #[Route('/transfer', name: 'store_transaction', methods: ['POST'])]
    public function store(Request $req): Response
    {
        $transaction = $this->transactionService->createTransaction($req);

        $this->transactionService->validate($transaction);
        $this->transactionService->execTransaction($transaction);

        return $this->json([
            'message' => 'Transfered successfully',
            'data' => $transaction,
        ], Response::HTTP_CREATED);
    }
}
