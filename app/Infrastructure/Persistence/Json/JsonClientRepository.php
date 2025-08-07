<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Json;

use App\Domain\Client\Entities\Client;
use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Client\ValueObjects\ClientPin;
use App\Domain\Client\ValueObjects\CreditScore;
use App\Domain\Client\ValueObjects\Email;
use App\Domain\Client\ValueObjects\Income;
use App\Domain\Client\ValueObjects\Phone;
use App\Domain\Client\ValueObjects\Region;
use Illuminate\Support\Facades\Storage;

final class JsonClientRepository implements ClientRepositoryInterface
{
    private const FILE_PATH = 'clients.json';

    public function save(Client $client): void
    {
        $clients = $this->loadClients();
        $clients[$client->getId()] = $this->clientToArray($client);
        $this->saveClients($clients);
    }

    public function findById(string $id): ?Client
    {
        $clients = $this->loadClients();

        if (! isset($clients[$id])) {
            return null;
        }

        return $this->arrayToClient($clients[$id]);
    }

    public function findByPin(string $pin): ?Client
    {
        $clients = $this->loadClients();

        foreach ($clients as $clientData) {
            if ($clientData['pin'] === $pin) {
                return $this->arrayToClient($clientData);
            }
        }

        return null;
    }

    public function exists(string $id): bool
    {
        $clients = $this->loadClients();

        return isset($clients[$id]);
    }

    private function loadClients(): array
    {
        if (! Storage::exists(self::FILE_PATH)) {
            return [];
        }

        $content = Storage::get(self::FILE_PATH);

        return json_decode($content, true) ?? [];
    }

    private function saveClients(array $clients): void
    {
        Storage::put(self::FILE_PATH, json_encode($clients, JSON_PRETTY_PRINT));
    }

    private function clientToArray(Client $client): array
    {
        return [
            'id' => $client->getId(),
            'name' => $client->getName(),
            'age' => $client->getAge(),
            'region' => $client->getRegion()->getCode(),
            'income' => $client->getIncome()->getAmount(),
            'score' => $client->getScore()->getValue(),
            'pin' => $client->getPin()->getValue(),
            'email' => $client->getEmail()->getValue(),
            'phone' => $client->getPhone()->getValue(),
        ];
    }

    private function arrayToClient(array $data): Client
    {
        return new Client(
            id: $data['id'],
            name: $data['name'],
            age: $data['age'],
            region: new Region($data['region']),
            income: new Income($data['income']),
            score: new CreditScore($data['score']),
            pin: new ClientPin($data['pin']),
            email: new Email($data['email']),
            phone: new Phone($data['phone'])
        );
    }
}
