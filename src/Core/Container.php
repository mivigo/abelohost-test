<?php

namespace App\Core;

use Psr\Container\ContainerInterface;
use App\Core\Exception\ContainerException;
use App\Core\Exception\NotFoundException;

class Container implements ContainerInterface
{
    private array $entries = [];

    /**
     * Bind a service or value to the container.
     */
    public function set(string $id, mixed $concrete): void
    {
        $this->entries[$id] = $concrete;
    }

    /**
     * Get a service from the container. Resolves callbacks on first access.
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException("Entry not found: " . $id);
        }

        $entry = $this->entries[$id];

        if (is_callable($entry)) {
            // Instantiate once (singleton behavior)
            $this->entries[$id] = $entry($this);
            return $this->entries[$id];
        }

        return $entry;
    }

    /**
     * Check if a service is bound to the container.
     */
    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }
}
