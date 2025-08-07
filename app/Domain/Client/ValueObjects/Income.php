<?php

declare(strict_types=1);

namespace App\Domain\Client\ValueObjects;

use InvalidArgumentException;

final readonly class Income
{
    public function __construct(private int $amount)
    {
        if ($amount < 0) {
            throw new InvalidArgumentException('Income cannot be negative');
        }
    }

    public function getAmount(): int
    {
        return $this->amount;
    }
}
