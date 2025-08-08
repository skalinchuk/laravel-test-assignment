<?php

declare(strict_types=1);

namespace App\Domain\Client\ValueObjects;

use InvalidArgumentException;

final readonly class Email
{
    public function __construct(private string $value)
    {
        if (! filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
