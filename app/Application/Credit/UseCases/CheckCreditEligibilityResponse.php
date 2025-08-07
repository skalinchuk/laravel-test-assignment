<?php

declare(strict_types=1);

namespace App\Application\Credit\UseCases;

use App\Domain\Credit\Entities\Credit;

final readonly class CheckCreditEligibilityResponse
{
    public function __construct(
        public bool $isEligible,
        public array $reasons,
        public ?Credit $finalCredit = null,
    ) {}
}
