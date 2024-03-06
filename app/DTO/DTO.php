<?php

namespace App\DTO;

interface DTO
{
    public function toArray(): array;

    public static function fromArray(array $data): static;
}
