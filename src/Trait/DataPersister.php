<?php

namespace App\Trait;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

trait DataPersister
{
    public function __construct(
        private EntityManagerInterface $em,
        private ValidatorInterface $v,
    ) {
    }

    public function validate($object): void
    {
        $erros = $this->v->validate($object);
        if (count($erros) > 0) {
            throw new \JsonException((string) $erros, Response::HTTP_BAD_REQUEST);
        }
    }

    public function persist(object $object, bool $flush = false): void
    {
        $this->em->persist($object);

        if ($flush) {
            $this->em->flush();
        }
    }
}
