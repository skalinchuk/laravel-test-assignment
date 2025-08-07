<?php

declare(strict_types=1);

namespace App\Application\CreditApplication\UseCases;

final readonly class GetClientApplicationsCommand
{
    public function __construct(public string $clientId) {}
}
