<?php

declare(strict_types=1);

namespace App\Domain\Client\Repositories;

use App\Domain\Client\Entities\Client;

interface ClientRepositoryInterface
{
    public function save(Client $client): void;

    public function findById(string $id): ?Client;

    public function findByPin(string $pin): ?Client;

    public function exists(string $id): bool;
}
