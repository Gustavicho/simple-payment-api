<?php

namespace App\Trait;

/**
 * Trait EntityDataManager.
 *
 * Provides methods for managing the `creation` and `update` of `entities`.
 * It automates the assignment of values to entity properties using setters
 * and ensures that the `createdAt` and `updatedAt` date properties are set
 * automatically during these processes.
 *
 * Methods:
 * - `create(array $data)`: self
 * - `update(array $data)`: self
 * - `setData(array $data)`: self
 */
trait EntityDataManager
{
    public function create(array $data): self
    {
        if (property_exists($this, 'createdAt')) {
            $this->setCreatedAt('now', 'America/Manaus');
        }

        return $this->setData($data);
    }

    public function update(array $data): self
    {
        if (property_exists($this, 'updatedAt')) {
            $this->setUpdatedAt('now', 'America/Manaus');
        }

        return $this->setData($data);
    }

    private function setData(array $data): self
    {
        foreach ($data as $property => $value) {
            // Make the `setter` name
            $method = 'set'.ucfirst($property);
            if (method_exists($this, $method)) {
                // call the setter
                $this->$method($value);
            }
        }

        return $this;
    }
}
