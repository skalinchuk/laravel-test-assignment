<?php

namespace App\Application\Credit\UseCases;

use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Credit\Repositories\CreditRepositoryInterface;
use App\Domain\Credit\Services\CreditApprovalService;
use InvalidArgumentException;

final readonly class CheckCreditEligibilityUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository,
        private CreditRepositoryInterface $creditRepository,
        private CreditApprovalService $approvalService,
    ) {}

    public function execute(CheckCreditEligibilityCommand $command): CheckCreditEligibilityResponse
    {
        $client = $this->clientRepository->findById($command->clientId);
        if ($client === null) {
            throw new InvalidArgumentException('Client not found');
        }

        $credit = $this->creditRepository->findById($command->creditId);
        if ($credit === null) {
            throw new InvalidArgumentException('Credit product not found');
        }

        $result = $this->approvalService->evaluate($client, $credit);

        return new CheckCreditEligibilityResponse(
            $result->isApproved(),
            $result->getReasons(),
            $result->getFinalCredit()
        );
    }
}
