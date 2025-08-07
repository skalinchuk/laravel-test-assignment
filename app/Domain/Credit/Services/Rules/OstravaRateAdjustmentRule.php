<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

final readonly class OstravaRateAdjustmentRule implements CreditApprovalRuleInterface
{
    public function evaluate(Client $client, Credit $credit): RuleResult
    {
        $rateIncrease = config('credit.adjustments.ostrava_rate_increase', 5.0);

        if ($client->isFromRegion('OS')) {
            $adjustedCredit = $credit->withAdjustedRate($rateIncrease);

            return RuleResult::pass(
                "Rate increased by {$rateIncrease}% for Ostrava region",
                $adjustedCredit
            );
        }

        return RuleResult::pass('No rate adjustment needed');
    }

    public function getPriority(): int
    {
        return 50;
    }
}
