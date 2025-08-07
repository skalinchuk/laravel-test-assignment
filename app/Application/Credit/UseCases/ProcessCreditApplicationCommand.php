<?php

declare(strict_types=1);

namespace App\Application\Credit\UseCases;

final readonly class ProcessCreditApplicationCommand
{
    public function __construct(
        public string $clientId,
        public string $creditId,
    ) {}
}
