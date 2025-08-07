<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

final readonly class PragueRandomRejectionRule implements CreditApprovalRuleInterface
{
    public function evaluate(Client $client, Credit $credit): RuleResult
    {
        // Check if feature is enabled
        if (! config('credit.features.prague_random_rejection', true)) {
            return RuleResult::pass('Prague random rejection disabled');
        }

        if ($client->isFromRegion('PR') && random_int(1, 2) === 1) {
            return RuleResult::fail('Random rejection for Prague region');
        }

        return RuleResult::pass('Prague random check passed');
    }

    public function getPriority(): int
    {
        return 60;
    }
}
