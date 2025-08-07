<?php

declare(strict_types=1);

namespace App\Application\CreditApplication\UseCases;

final readonly class GetCreditApplicationCommand
{
    public function __construct(public string $applicationId) {}
}
