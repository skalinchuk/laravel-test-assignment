<?php

declare(strict_types=1);

namespace App\Application\Client\UseCases;

use App\Domain\Client\Repositories\ClientRepositoryInterface;
use InvalidArgumentException;

final readonly class GetClientUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(GetClientCommand $command): GetClientResponse
    {
        $client = $this->clientRepository->findById($command->clientId);
        if ($client === null) {
            throw new InvalidArgumentException('Client not found');
        }

        return new GetClientResponse(
            $client->getId(),
            $client->getName(),
            $client->getAge(),
            $client->getRegion()->getCode(),
            $client->getIncome()->getAmount(),
            $client->getScore()->getValue(),
            $client->getPin()->getValue(),
            $client->getEmail()->getValue(),
            $client->getPhone()->getValue()
        );
    }
}
