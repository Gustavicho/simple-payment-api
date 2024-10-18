<?php

namespace App\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait DataPersister
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function validate($object): void
    {
        $erros = $this->validator->validate($object);
        if (count($erros) > 0) {
            throw new \JsonException((string) $erros, Response::HTTP_BAD_REQUEST);
        }
    }

    public function persist(object $object, bool $flush = false): void
    {
        $this->entityManager->persist($object);

        if ($flush) {
            $this->entityManager->flush();
        }
    }
}
