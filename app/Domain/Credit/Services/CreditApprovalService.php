<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services;

use App\Domain\Client\Entities\Client;
use App\Domain\Credit\Entities\Credit;
use App\Domain\Credit\Services\Rules\CreditApprovalRuleInterface;

final readonly class ApprovalResult
{
    public function __construct(
        private bool $approved,
        private array $reasons,
        private ?Credit $finalCredit = null
    ) {}

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function getReasons(): array
    {
        return $this->reasons;
    }

    public function getFinalCredit(): ?Credit
    {
        return $this->finalCredit;
    }
}

final class CreditApprovalService
{
    /** @var CreditApprovalRuleInterface[] */
    private array $rules = [];

    public function addRule(CreditApprovalRuleInterface $rule): void
    {
        $this->rules[] = $rule;

        // Sort by priority (higher priority first)
        usort($this->rules, fn ($a, $b) => $b->getPriority() <=> $a->getPriority());
    }

    public function evaluate(Client $client, Credit $credit): ApprovalResult
    {
        $reasons = [];
        $currentCredit = $credit;

        foreach ($this->rules as $rule) {
            $result = $rule->evaluate($client, $currentCredit);
            $reasons[] = $result->getReason();

            if (! $result->passed()) {
                return new ApprovalResult(false, $reasons);
            }

            // If rule modified the credit, use the modified version
            if ($result->getModifiedCredit() !== null) {
                $currentCredit = $result->getModifiedCredit();
            }
        }

        return new ApprovalResult(true, $reasons, $currentCredit);
    }
}
