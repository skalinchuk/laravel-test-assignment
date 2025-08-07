<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

final readonly class AgeRule implements CreditApprovalRuleInterface
{
    public function __construct(
        private ?int $minAge = null,
        private ?int $maxAge = null
    ) {}

    public function evaluate(Client $client, Credit $credit): RuleResult
    {
        $minAge = $this->minAge ?? config('credit.age.min', 18);
        $maxAge = $this->maxAge ?? config('credit.age.max', 60);

        if (! $client->isAgeInRange($minAge, $maxAge)) {
            return RuleResult::fail("Age must be between {$minAge} and {$maxAge}");
        }

        return RuleResult::pass('Age requirement met');
    }

    public function getPriority(): int
    {
        return 100;
    }
}
