<?php

declare(strict_types=1);

namespace App\Application\CreditApplication\UseCases;

final readonly class GetCreditApplicationResponse
{
    public function __construct(
        public string $id,
        public string $clientId,
        public string $creditId,
        public string $status,
        public string $appliedAt,
        public ?string $processedAt,
        public array $reasons,
        public ?array $finalCredit,
    ) {}
}
