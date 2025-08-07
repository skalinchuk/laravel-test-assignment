<?php

declare(strict_types=1);

namespace App\Domain\Client\Entities;

use App\Domain\Client\ValueObjects\ClientPin;
use App\Domain\Client\ValueObjects\CreditScore;
use App\Domain\Client\ValueObjects\Email;
use App\Domain\Client\ValueObjects\Income;
use App\Domain\Client\ValueObjects\Phone;
use App\Domain\Client\ValueObjects\Region;

final readonly class Client
{
    public function __construct(
        private string $id,
        private string $name,
        private int $age,
        private Region $region,
        private Income $income,
        private CreditScore $score,
        private ClientPin $pin,
        private Email $email,
        private Phone $phone,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function getIncome(): Income
    {
        return $this->income;
    }

    public function getScore(): CreditScore
    {
        return $this->score;
    }

    public function getPin(): ClientPin
    {
        return $this->pin;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPhone(): Phone
    {
        return $this->phone;
    }

    public function isAgeInRange(int $minAge, int $maxAge): bool
    {
        return $this->age >= $minAge && $this->age <= $maxAge;
    }

    public function hasMinimumIncome(int $minimumAmount): bool
    {
        return $this->income->getAmount() >= $minimumAmount;
    }

    public function hasMinimumScore(int $minimumScore): bool
    {
        return $this->score->getValue() > $minimumScore;
    }

    public function isFromRegion(string $regionCode): bool
    {
        return $this->region->getCode() === $regionCode;
    }
}
