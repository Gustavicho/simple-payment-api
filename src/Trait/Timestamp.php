<?php

namespace App\Trait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait Timestamp.
 *
 * The `Timestamp` trait manages automatic timestamps for entity creation (`createdAt`)
 * and updates (`updatedAt`). It can be used in any entity that requires these fields.
 *
 * Attributes:
 * - `createdAt`: Stores the entity's creation timestamp. Set automatically on persist (`PrePersist`).
 * - `updatedAt`: Stores the entity's last update timestamp. Set automatically on update (`PreUpdate`).
 *
 * Methods:
 * - `getCreatedAt()`: Returns the `createdAt` timestamp or `null` if not set.
 * - `getUpdatedAt()`: Returns the `updatedAt` timestamp or `null` if not set.
 * - `setCreatedAt()`: Sets `createdAt` to the current time (default: 'now', timezone: 'UTC'). Called before persisting.
 * - `setUpdatedAt()`: Sets `updatedAt` to the current time (default: 'now', timezone: 'UTC'). Called before updating.
 */
#[ORM\HasLifecycleCallbacks]
trait Timestamp
{
    #[ORM\Column(name: 'created_at')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(name: 'updated_at', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    #[ORM\PrePersist]
    public function setCreatedAt(string $time = 'now', string $timezone = 'UTC'): static
    {
        $this->createdAt = new \DateTimeImmutable($time, new \DateTimeZone($timezone));

        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedAt(string $time = 'now', string $timezone = 'UTC'): static
    {
        $this->updatedAt = new \DateTimeImmutable($time, new \DateTimeZone($timezone));

        return $this;
    }
}
