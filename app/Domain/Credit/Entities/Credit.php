<?php

declare(strict_types=1);

namespace App\Domain\Credit\Entities;

use App\Domain\Credit\ValueObjects\CreditAmount;
use App\Domain\Credit\ValueObjects\InterestRate;
use DateTimeImmutable;

final readonly class Credit
{
    public function __construct(
        private string $id,
        private string $name,
        private CreditAmount $amount,
        private InterestRate $rate,
        private DateTimeImmutable $startDate,
        private DateTimeImmutable $endDate,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAmount(): CreditAmount
    {
        return $this->amount;
    }

    public function getRate(): InterestRate
    {
        return $this->rate;
    }

    public function getStartDate(): DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): DateTimeImmutable
    {
        return $this->endDate;
    }

    public function withAdjustedRate(float $adjustment): self
    {
        $newRate = new InterestRate($this->rate->getValue() + $adjustment);

        return new self(
            $this->id,
            $this->name,
            $this->amount,
            $newRate,
            $this->startDate,
            $this->endDate
        );
    }
}
