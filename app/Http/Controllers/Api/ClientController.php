<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Client\UseCases\CreateClientCommand;
use App\Application\Client\UseCases\CreateClientUseCase;
use App\Application\Client\UseCases\GetClientCommand;
use App\Application\Client\UseCases\GetClientUseCase;
use App\Http\Requests\CreateClientRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use InvalidArgumentException;

final class ClientController extends Controller
{
    public function __construct(
        private readonly CreateClientUseCase $createClientUseCase,
        private readonly GetClientUseCase $getClientUseCase,
    ) {}

    public function store(CreateClientRequest $request): JsonResponse
    {
        try {
            $command = new CreateClientCommand(
                name: $request->validated('name'),
                age: $request->validated('age'),
                region: $request->validated('region'),
                income: $request->validated('income'),
                score: $request->validated('score'),
                pin: $request->validated('pin'),
                email: $request->validated('email'),
                phone: $request->validated('phone'),
            );

            $response = $this->createClientUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'client_id' => $response->clientId,
                ],
                'message' => 'Client created successfully',
            ], Response::HTTP_CREATED);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function show(string $id): JsonResponse
    {
        try {
            $command = new GetClientCommand($id);
            $response = $this->getClientUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $response->id,
                    'name' => $response->name,
                    'age' => $response->age,
                    'region' => $response->region,
                    'income' => $response->income,
                    'score' => $response->score,
                    'pin' => $response->pin,
                    'email' => $response->email,
                    'phone' => $response->phone,
                ],
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }
}
