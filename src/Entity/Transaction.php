<?php

namespace App\Entity;

use App\Repository\TransactionRepository;
use App\Trait\Timestamp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: '`transactions`')]
#[Groups(['transaction:read'])]
class Transaction
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['transaction:write'])]
    private ?string $value = null;

    #[ORM\ManyToOne(inversedBy: 'sentTransactions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['transaction:write'])]
    private ?User $sender = null;

    #[ORM\ManyToOne(inversedBy: 'receivedTransactions', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['transaction:write'])]
    private ?User $receiver = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }
}
