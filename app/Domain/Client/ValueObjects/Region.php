<?php

declare(strict_types=1);

namespace App\Domain\Client\ValueObjects;

final readonly class Region
{
    public function __construct(private string $code) {}

    public function getCode(): string
    {
        return $this->code;
    }

    public static function getAllowedRegions(): array
    {
        return config('credit.regions.allowed', []);
    }
}
