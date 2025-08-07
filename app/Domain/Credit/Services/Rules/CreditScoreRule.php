<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

final readonly class CreditScoreRule implements CreditApprovalRuleInterface
{
    public function __construct(private ?int $minimumScore = null) {}

    public function evaluate(Client $client, Credit $credit): RuleResult
    {
        $minimumScore = $this->minimumScore ?? config('credit.score.minimum', 500);

        if (! $client->hasMinimumScore($minimumScore)) {
            return RuleResult::fail("Credit score must be greater than {$minimumScore}");
        }

        return RuleResult::pass('Credit score requirement met');
    }

    public function getPriority(): int
    {
        return 90;
    }
}
