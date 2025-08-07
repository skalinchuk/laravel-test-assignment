<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;

final readonly class RegionRule implements CreditApprovalRuleInterface
{
    public function evaluate(Client $client, Credit $credit): RuleResult
    {
        $allowedRegions = config('credit.regions.allowed', ['PR', 'BR', 'OS']);
        $regionCode = $client->getRegion()->getCode();

        if (! in_array($regionCode, $allowedRegions, true)) {
            return RuleResult::fail('Credit not available in your region');
        }

        return RuleResult::pass('Region requirement met');
    }

    public function getPriority(): int
    {
        return 70;
    }
}
