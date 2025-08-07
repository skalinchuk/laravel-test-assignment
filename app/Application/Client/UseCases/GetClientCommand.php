<?php

declare(strict_types=1);

namespace App\Application\Client\UseCases;

final readonly class GetClientCommand
{
    public function __construct(public string $clientId) {}
}
