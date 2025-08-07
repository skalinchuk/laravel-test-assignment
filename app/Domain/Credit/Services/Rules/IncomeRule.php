<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

final readonly class IncomeRule implements CreditApprovalRuleInterface
{
    public function __construct(private ?int $minimumIncome = null) {}

    public function evaluate(Client $client, Credit $credit): RuleResult
    {
        $minimumIncome = $this->minimumIncome ?? config('credit.income.minimum', 1000);

        if (! $client->hasMinimumIncome($minimumIncome)) {
            return RuleResult::fail("Monthly income must be at least \${$minimumIncome}");
        }

        return RuleResult::pass('Income requirement met');
    }

    public function getPriority(): int
    {
        return 80;
    }
}
