<?php

declare(strict_types=1);

namespace App\Domain\Credit\Services\Rules;

use App\Domain\Credit\Entities\Credit;

final readonly class RuleResult
{
    public function __construct(
        private bool $passed,
        private string $reason = '',
        private ?Credit $modifiedCredit = null
    ) {}

    public function passed(): bool
    {
        return $this->passed;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getModifiedCredit(): ?Credit
    {
        return $this->modifiedCredit;
    }

    public static function pass(string $reason = '', ?Credit $modifiedCredit = null): self
    {
        return new self(true, $reason, $modifiedCredit);
    }

    public static function fail(string $reason): self
    {
        return new self(false, $reason);
    }
}
