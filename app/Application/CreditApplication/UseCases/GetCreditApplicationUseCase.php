<?php

declare(strict_types=1);

namespace App\Application\CreditApplication\UseCases;

use App\Domain\CreditApplication\Repositories\CreditApplicationRepositoryInterface;
use InvalidArgumentException;

final readonly class GetCreditApplicationUseCase
{
    public function __construct(
        private CreditApplicationRepositoryInterface $applicationRepository
    ) {}

    public function execute(GetCreditApplicationCommand $command): GetCreditApplicationResponse
    {
        $application = $this->applicationRepository->findById($command->applicationId);
        if ($application === null) {
            throw new InvalidArgumentException('Credit application not found');
        }

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

        return new GetCreditApplicationResponse(
            $application->getId(),
            $application->getClientId(),
            $application->getCreditId(),
            $application->getStatus()->value,
            $application->getAppliedAt()->format('Y-m-d H:i:s'),
            $application->getProcessedAt()?->format('Y-m-d H:i:s'),
            $application->getReasons(),
            $finalCreditData
        );
    }
}
