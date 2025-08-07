<?php

declare(strict_types=1);

namespace App\Domain\Credit\ValueObjects;

use InvalidArgumentException;

final readonly class InterestRate
{
    public function __construct(private float $value)
    {
        if ($value < 0 || $value > 100) {
            throw new InvalidArgumentException('Interest rate must be between 0 and 100');
        }
    }

    public function getValue(): float
    {
        return $this->value;
    }
}
