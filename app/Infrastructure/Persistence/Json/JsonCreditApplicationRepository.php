<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Json;

use App\Domain\Credit\Entities\Credit;
use App\Domain\Credit\ValueObjects\CreditAmount;
use App\Domain\Credit\ValueObjects\InterestRate;
use App\Domain\CreditApplication\Entities\ApplicationStatus;
use App\Domain\CreditApplication\Entities\CreditApplication;
use App\Domain\CreditApplication\Repositories\CreditApplicationRepositoryInterface;
use DateTimeImmutable;
use Illuminate\Support\Facades\Storage;

final class JsonCreditApplicationRepository implements CreditApplicationRepositoryInterface
{
    private const FILE_PATH = 'credit_applications.json';

    public function save(CreditApplication $application): void
    {
        $applications = $this->loadApplications();
        $applications[$application->getId()] = $this->applicationToArray($application);
        $this->saveApplications($applications);
    }

    public function findById(string $id): ?CreditApplication
    {
        $applications = $this->loadApplications();

        if (! isset($applications[$id])) {
            return null;
        }

        return $this->arrayToApplication($applications[$id]);
    }

    public function findByClientId(string $clientId): array
    {
        $applications = $this->loadApplications();

        $result = [];
        foreach ($applications as $applicationData) {
            if ($applicationData['client_id'] === $clientId) {
                $result[] = $this->arrayToApplication($applicationData);
            }
        }

        return $result;
    }

    private function loadApplications(): array
    {
        if (! Storage::exists(self::FILE_PATH)) {
            return [];
        }

        $content = Storage::get(self::FILE_PATH);

        return json_decode($content, true) ?? [];
    }

    private function saveApplications(array $applications): void
    {
        Storage::put(self::FILE_PATH, json_encode($applications, JSON_PRETTY_PRINT));
    }

    private function applicationToArray(CreditApplication $application): array
    {
        $data = [
            'id' => $application->getId(),
            'client_id' => $application->getClientId(),
            'credit_id' => $application->getCreditId(),
            'status' => $application->getStatus()->value,
            'applied_at' => $application->getAppliedAt()->format('Y-m-d H:i:s'),
            'processed_at' => $application->getProcessedAt()?->format('Y-m-d H:i:s'),
            'reasons' => $application->getReasons(),
            'final_credit' => null,
        ];

        if ($application->getFinalCredit() !== null) {
            $credit = $application->getFinalCredit();
            $data['final_credit'] = [
                'id' => $credit->getId(),
                'name' => $credit->getName(),
                'amount' => $credit->getAmount()->getAmount(),
                'rate' => $credit->getRate()->getValue(),
                'start_date' => $credit->getStartDate()->format('Y-m-d'),
                'end_date' => $credit->getEndDate()->format('Y-m-d'),
            ];
        }

        return $data;
    }

    private function arrayToApplication(array $data): CreditApplication
    {
        $finalCredit = null;
        if ($data['final_credit'] !== null) {
            $creditData = $data['final_credit'];
            $finalCredit = new Credit(
                id: $creditData['id'],
                name: $creditData['name'],
                amount: new CreditAmount($creditData['amount']),
                rate: new InterestRate($creditData['rate']),
                startDate: new DateTimeImmutable($creditData['start_date']),
                endDate: new DateTimeImmutable($creditData['end_date'])
            );
        }

        return new CreditApplication(
            id: $data['id'],
            clientId: $data['client_id'],
            creditId: $data['credit_id'],
            status: ApplicationStatus::from($data['status']),
            appliedAt: new DateTimeImmutable($data['applied_at']),
            processedAt: $data['processed_at'] ? new DateTimeImmutable($data['processed_at']) : null,
            reasons: $data['reasons'],
            finalCredit: $finalCredit
        );
    }
}
