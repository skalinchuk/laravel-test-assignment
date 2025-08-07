<?php

declare(strict_types=1);

namespace App\Application\Client\UseCases;

final readonly class GetClientResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public int $age,
        public string $region,
        public int $income,
        public int $score,
        public string $pin,
        public string $email,
        public string $phone,
    ) {}
}
