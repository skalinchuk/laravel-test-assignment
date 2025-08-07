<?php

declare(strict_types=1);

namespace App\Domain\Client\ValueObjects;

use InvalidArgumentException;

final readonly class ClientPin
{
    public function __construct(private string $value)
    {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('PIN cannot be empty');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
