<?php

namespace App\Controller;

use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public function __construct(
        private UserService $userService,
    ) {
    }

    #[Route('/users', name: 'list_users', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json(
            $this->userService->findAll(),
            context: ['groups' => ['user:read']]
        );
    }

    #[Route('/users', name: 'store_user', methods: ['POST'])]
    public function store(Request $req): Response
    {
        $user = $this->userService->createUser($req);

        $this->userService->validate($user);
        $this->userService->persist($user, true);

        return $this->json(
            [
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user,
            ],
            Response::HTTP_CREATED,
            context: ['groups' => ['user:read']]
        );
    }
}
