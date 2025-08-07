<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Json;

use App\Domain\Credit\Entities\Credit;
use App\Domain\Credit\Repositories\CreditRepositoryInterface;
use App\Domain\Credit\ValueObjects\CreditAmount;
use App\Domain\Credit\ValueObjects\InterestRate;
use DateTimeImmutable;
use Illuminate\Support\Facades\Storage;

final class JsonCreditRepository implements CreditRepositoryInterface
{
    private const FILE_PATH = 'credits.json';

    public function __construct()
    {
        $this->initializeDefaults();
    }

    /**
     * @throws \DateMalformedStringException
     */
    public function findById(string $id): ?Credit
    {
        $credits = $this->loadCredits();

        if (! isset($credits[$id])) {
            return null;
        }

        return $this->arrayToCredit($credits[$id]);
    }

    /**
     * @return array<Credit>
     */
    public function findAll(): array
    {
        $credits = $this->loadCredits();

        return array_map([$this, 'arrayToCredit'], array_values($credits));
    }

    public function save(Credit $credit): void
    {
        $credits = $this->loadCredits();
        $credits[$credit->getId()] = $this->creditToArray($credit);
        $this->saveCredits($credits);
    }

    private function loadCredits(): array
    {
        if (! Storage::exists(self::FILE_PATH)) {
            return [];
        }

        $content = Storage::get(self::FILE_PATH);

        return json_decode($content, true) ?? [];
    }

    private function saveCredits(array $credits): void
    {
        Storage::put(self::FILE_PATH, json_encode($credits, JSON_PRETTY_PRINT));
    }

    private function creditToArray(Credit $credit): array
    {
        return [
            'id' => $credit->getId(),
            'name' => $credit->getName(),
            'amount' => $credit->getAmount()->getAmount(),
            'rate' => $credit->getRate()->getValue(),
            'start_date' => $credit->getStartDate()->format('Y-m-d'),
            'end_date' => $credit->getEndDate()->format('Y-m-d'),
        ];
    }

    /**
     * @throws \DateMalformedStringException
     */
    private function arrayToCredit(array $data): Credit
    {
        return new Credit(
            id: $data['id'],
            name: $data['name'],
            amount: new CreditAmount($data['amount']),
            rate: new InterestRate($data['rate']),
            startDate: new DateTimeImmutable($data['start_date']),
            endDate: new DateTimeImmutable($data['end_date'])
        );
    }

    private function initializeDefaults(): void
    {
        if (Storage::exists(self::FILE_PATH)) {
            return;
        }

        $defaultCredits = [
            'personal-loan' => [
                'id' => 'personal-loan',
                'name' => 'Personal Loan',
                'amount' => 1000.0,
                'rate' => 10.0,
                'start_date' => '2024-01-01',
                'end_date' => '2024-12-31',
            ],
            'business-loan' => [
                'id' => 'business-loan',
                'name' => 'Business Loan',
                'amount' => 5000.0,
                'rate' => 12.5,
                'start_date' => '2024-01-01',
                'end_date' => '2025-12-31',
            ],
        ];

        $this->saveCredits($defaultCredits);
    }
}
