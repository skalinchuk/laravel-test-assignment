<?php

declare(strict_types=1);

namespace App\Application\CreditApplication\UseCases;

final readonly class GetClientApplicationsResponse
{
    public function __construct(public array $applications) {}
}
