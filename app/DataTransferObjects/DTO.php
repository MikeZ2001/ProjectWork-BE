<?php

namespace App\DataTransferObjects;

abstract readonly class DTO
{
    /**
     * Returns DTO properties as an associative array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}