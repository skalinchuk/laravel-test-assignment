<?php

declare(strict_types=1);

namespace App\Application\Credit\UseCases;

final readonly class GetAvailableCreditsResponse
{
    public function __construct(public array $credits) {}
}
