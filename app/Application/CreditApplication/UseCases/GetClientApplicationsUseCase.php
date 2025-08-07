<?php

declare(strict_types=1);

namespace App\Application\CreditApplication\UseCases;

use App\Domain\CreditApplication\Repositories\CreditApplicationRepositoryInterface;

final readonly class GetClientApplicationsUseCase
{
    public function __construct(
        private CreditApplicationRepositoryInterface $applicationRepository
    ) {}

    public function execute(GetClientApplicationsCommand $command): GetClientApplicationsResponse
    {
        $applications = $this->applicationRepository->findByClientId($command->clientId);

        $applicationData = array_map(function ($application) {
            $finalCreditData = null;
            if ($application->getFinalCredit() !== null) {
                $credit = $application->getFinalCredit();
                $finalCreditData = [
                    'id' => $credit->getId(),
                    'name' => $credit->getName(),
                    'amount' => $credit->getAmount()->getAmount(),
                    'rate' => $credit->getRate()->getValue(),
                ];
            }

            return [
                'id' => $application->getId(),
                'credit_id' => $application->getCreditId(),
                'status' => $application->getStatus()->value,
                'applied_at' => $application->getAppliedAt()->format('Y-m-d H:i:s'),
                'processed_at' => $application->getProcessedAt()?->format('Y-m-d H:i:s'),
                'reasons' => $application->getReasons(),
                'final_credit' => $finalCreditData,
            ];
        }, $applications);

        return new GetClientApplicationsResponse($applicationData);
    }
}
