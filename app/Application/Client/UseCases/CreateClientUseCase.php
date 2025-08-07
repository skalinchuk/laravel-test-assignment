<?php

declare(strict_types=1);

namespace App\Application\Client\UseCases;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Client\ValueObjects\ClientPin;
use App\Domain\Client\ValueObjects\CreditScore;
use App\Domain\Client\ValueObjects\Email;
use App\Domain\Client\ValueObjects\Income;
use App\Domain\Client\ValueObjects\Phone;
use App\Domain\Client\ValueObjects\Region;
use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class CreateClientUseCase
{
    public function __construct(
        private ClientRepositoryInterface $clientRepository
    ) {}

    public function execute(CreateClientCommand $command): CreateClientResponse
    {
        // Check if client with this PIN already exists
        if ($this->clientRepository->findByPin($command->pin) !== null) {
            throw new InvalidArgumentException('Client with this PIN already exists');
        }

        $client = new Client(
            id: Str::uuid()->toString(),
            name: $command->name,
            age: $command->age,
            region: new Region($command->region),
            income: new Income($command->income),
            score: new CreditScore($command->score),
            pin: new ClientPin($command->pin),
            email: new Email($command->email),
            phone: new Phone($command->phone)
        );

        $this->clientRepository->save($client);

        return new CreateClientResponse($client->getId());
    }
}
