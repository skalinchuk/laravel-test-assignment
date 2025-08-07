<?php

declare(strict_types=1);

namespace App\Application\Credit\UseCases;

use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Credit\Repositories\CreditRepositoryInterface;
use App\Domain\Credit\Services\CreditApprovalService;
use App\Domain\CreditApplication\Entities\ApplicationStatus;
use App\Domain\CreditApplication\Entities\CreditApplication;
use App\Domain\CreditApplication\Repositories\CreditApplicationRepositoryInterface;
use App\Domain\Notification\Services\NotificationServiceInterface;
use DateTimeImmutable;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class ProcessCreditApplicationUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private CreditRepositoryInterface $creditRepository,
        private CreditApplicationRepositoryInterface $applicationRepository,
        private CreditApprovalService $approvalService,
        private NotificationServiceInterface $notificationService,
    ) {}

    public function execute(ProcessCreditApplicationCommand $command): ProcessCreditApplicationResponse
    {
        $client = $this->clientRepository->findById($command->clientId);
        if ($client === null) {
            throw new InvalidArgumentException('Client not found');
        }

        $credit = $this->creditRepository->findById($command->creditId);
        if ($credit === null) {
            throw new InvalidArgumentException('Credit product not found');
        }

        // Create application
        $application = new CreditApplication(
            id: Str::uuid()->toString(),
            clientId: $command->clientId,
            creditId: $command->creditId,
            status: ApplicationStatus::PENDING,
            appliedAt: new DateTimeImmutable
        );

        // Evaluate application
        $result = $this->approvalService->evaluate($client, $credit);

        if ($result->isApproved()) {
            $application->approve($result->getReasons(), $result->getFinalCredit());
            $this->notificationService->notifyApproval($client, $application);
        } else {
            $application->reject($result->getReasons());
            $this->notificationService->notifyRejection($client, $application);
        }

        $this->applicationRepository->save($application);

        return new ProcessCreditApplicationResponse(
            $application->getId(),
            $application->isApproved(),
            $application->getReasons(),
            $application->getFinalCredit()
        );
    }
}
