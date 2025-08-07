<?php

declare(strict_types=1);

namespace App\Domain\CreditApplication\Repositories;

use App\Domain\CreditApplication\Entities\CreditApplication;

interface CreditApplicationRepositoryInterface
{
    public function findById(string $id): ?CreditApplication;

    public function findByClientId(string $clientId): array;

    public function save(CreditApplication $application): void;
}
