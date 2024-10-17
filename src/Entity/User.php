<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Trait\Timestamp;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`users`')]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_DOCUMENT', fields: ['document'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use Timestamp;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['transaction:read'])]
    private ?int $id = null;

    #[Assert\Email]
    #[Assert\NotNull]
    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[Assert\Count(min: 1, minMessage: 'The user must have at least one role.')]
    #[ORM\Column]
    #[Groups(['user:write'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[Assert\NotNull]
    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;

    #[Assert\NotNull]
    #[ORM\Column(length: 255)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $fullName = null;

    #[Assert\NotNull]
    #[Assert\Length(min: 14, max: 18)]
    #[ORM\Column(length: 18, unique: true)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $document = null;

    #[Assert\NotNull]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups(['user:read', 'user:write'])]
    private ?string $balance = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'sender')]
    #[Groups(['user:read', 'user:write'])]
    private Collection $sentTransactions;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'receiver')]
    #[Groups(['user:read', 'user:write'])]
    private Collection $receivedTransactions;

    public function __construct()
    {
        $this->sentTransactions = new ArrayCollection();
        $this->receivedTransactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(string $document): static
    {
        $this->document = $document;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionsSent(): Collection
    {
        return $this->sentTransactions;
    }

    public function addSentTransaction(Transaction $transaction): static
    {
        if (!$this->sentTransactions->contains($transaction)) {
            $this->sentTransactions->add($transaction);
            $transaction->setSender($this);
        }

        return $this;
    }

    public function removeSentTransaction(Transaction $transaction): static
    {
        if ($this->sentTransactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSender() === $this) {
                $transaction->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionsReceived(): Collection
    {
        return $this->receivedTransactions;
    }

    public function addReceivedTransaction(Transaction $transaction): static
    {
        if (!$this->receivedTransactions->contains($transaction)) {
            $this->receivedTransactions->add($transaction);
            $transaction->setSender($this);
        }

        return $this;
    }

    public function removeReceivedTransaction(Transaction $transaction): static
    {
        if ($this->receivedTransactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getSender() === $this) {
                $transaction->setSender(null);
            }
        }

        return $this;
    }
}
