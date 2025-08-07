<?php

declare(strict_types=1);

namespace App\Domain\Client\ValueObjects;

use InvalidArgumentException;

final readonly class CreditScore
{
    public function __construct(private int $value)
    {
        if ($value < 0 || $value > 1000) {
            throw new InvalidArgumentException('Credit score must be between 0 and 1000');
        }
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
