<?php

declare(strict_types=1);

namespace App\Application\Credit\UseCases;

final readonly class ProcessCreditApplicationResponse
{
    public function __construct(
        public string $applicationId,
        public bool $approved,
        public array $reasons,
        public ?\App\Domain\Credit\Entities\Credit $finalCredit = null,
    ) {}
}
