<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Http\Response;
use Tests\TestCase;

final class ClientControllerTest extends TestCase
{
    public function test_create_client_successfully(): void
    {
        $clientData = [
            'name' => 'Petr Pavel',
            'age' => 35,
            'region' => 'PR',
            'income' => 1500,
            'score' => 600,
            'pin' => '123-45-6789',
            'email' => 'petr.pavel@example.com',
            'phone' => '+420123456789',
        ];

        $response = $this->postJson('/api/v1/clients', $clientData);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Client created successfully',
            ])
            ->assertJsonStructure([
                'data' => ['client_id'],
            ]);

        // Verify the client ID is a valid UUID
        $clientId = $response->json('data.client_id');
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $clientId
        );
    }

    public function test_create_client_fails_with_invalid_data(): void
    {
        $clientData = [
            'name' => '',
            'age' => -1,
            'region' => 'INVALID',
            'income' => -100,
            'score' => 1500,
            'pin' => '',
            'email' => 'invalid-email',
            'phone' => '',
        ];

        $response = $this->postJson('/api/v1/clients', $clientData);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors([
                'name', 'age', 'region', 'income', 'score', 'pin', 'email', 'phone',
            ]);
    }

    public function test_create_client_fails_with_duplicate_pin(): void
    {
        $clientData = [
            'name' => 'First Client',
            'age' => 30,
            'region' => 'PR',
            'income' => 1500,
            'score' => 600,
            'pin' => '123-45-6789',
            'email' => 'first@example.com',
            'phone' => '+420123456789',
        ];

        // Create first client
        $this->postJson('/api/v1/clients', $clientData)
            ->assertStatus(Response::HTTP_CREATED);

        // Try to create second client with same PIN
        $clientData['name'] = 'Second Client';
        $clientData['email'] = 'second@example.com';

        $response = $this->postJson('/api/v1/clients', $clientData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'success' => false,
                'message' => 'Client with this PIN already exists',
            ]);
    }
}
