<?php

declare(strict_types=1);

namespace App\Domain\CreditApplication\Entities;

use App\Domain\Credit\Entities\Credit;
use DateTimeImmutable;

final class CreditApplication
{
    public function __construct(
        private readonly string $id,
        private readonly string $clientId,
        private readonly string $creditId,
        private ApplicationStatus $status,
        private readonly DateTimeImmutable $appliedAt,
        private ?DateTimeImmutable $processedAt = null,
        private array $reasons = [],
        private ?Credit $finalCredit = null,
    ) {}

    public function getId(): string
    {
        return $this->id;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getCreditId(): string
    {
        return $this->creditId;
    }

    public function getStatus(): ApplicationStatus
    {
        return $this->status;
    }

    public function getAppliedAt(): DateTimeImmutable
    {
        return $this->appliedAt;
    }

    public function getProcessedAt(): ?DateTimeImmutable
    {
        return $this->processedAt;
    }

    public function getReasons(): array
    {
        return $this->reasons;
    }

    public function getFinalCredit(): ?Credit
    {
        return $this->finalCredit;
    }

    public function approve(array $reasons, ?Credit $finalCredit = null): void
    {
        $this->status = ApplicationStatus::APPROVED;
        $this->processedAt = new DateTimeImmutable;
        $this->reasons = $reasons;
        $this->finalCredit = $finalCredit;
    }

    public function reject(array $reasons): void
    {
        $this->status = ApplicationStatus::REJECTED;
        $this->processedAt = new DateTimeImmutable;
        $this->reasons = $reasons;
    }

    public function isApproved(): bool
    {
        return $this->status === ApplicationStatus::APPROVED;
    }

    public function isRejected(): bool
    {
        return $this->status === ApplicationStatus::REJECTED;
    }

    public function isPending(): bool
    {
        return $this->status === ApplicationStatus::PENDING;
    }
}
