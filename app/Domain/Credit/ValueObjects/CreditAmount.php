<?php

declare(strict_types=1);

namespace App\Domain\Credit\ValueObjects;

use InvalidArgumentException;

final readonly class CreditAmount
{
    public function __construct(private float $amount)
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('Credit amount must be positive');
        }
    }

    public function getAmount(): float
    {
        return $this->amount;
    }
}
