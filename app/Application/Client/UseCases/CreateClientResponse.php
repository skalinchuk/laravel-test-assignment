<?php

namespace App\Application\Client\UseCases;

final readonly class CreateClientResponse
{
    public function __construct(public string $clientId) {}
}
