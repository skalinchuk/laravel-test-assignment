<?php

declare(strict_types=1);

namespace App\Domain\Credit\Repositories;

use App\Domain\Credit\Entities\Credit;

interface CreditRepositoryInterface
{
    public function findById(string $id): ?Credit;

    public function findAll(): array;

    public function save(Credit $credit): void;
}
