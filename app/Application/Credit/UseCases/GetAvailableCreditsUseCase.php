<?php

declare(strict_types=1);

namespace App\Application\Credit\UseCases;

use App\Domain\Credit\Repositories\CreditRepositoryInterface;

final readonly class GetAvailableCreditsUseCase
{
    public function __construct(
        private CreditRepositoryInterface $creditRepository
    ) {}

    public function execute(): GetAvailableCreditsResponse
    {
        $credits = $this->creditRepository->findAll();

        $creditData = array_map(function ($credit) {
            return [
                'id' => $credit->getId(),
                'name' => $credit->getName(),
                'amount' => $credit->getAmount()->getAmount(),
                'rate' => $credit->getRate()->getValue(),
                'start_date' => $credit->getStartDate()->format('Y-m-d'),
                'end_date' => $credit->getEndDate()->format('Y-m-d'),
            ];
        }, $credits);

        return new GetAvailableCreditsResponse($creditData);
    }
}
