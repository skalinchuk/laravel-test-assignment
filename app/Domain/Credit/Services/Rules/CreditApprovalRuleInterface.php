<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

interface CreditApprovalRuleInterface
{
    public function evaluate(Client $client, Credit $credit): RuleResult;

    public function getPriority(): int;
}
