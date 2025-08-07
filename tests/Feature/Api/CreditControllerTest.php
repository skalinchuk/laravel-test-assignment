<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use Illuminate\Http\Response;
use Tests\TestCase;

final class CreditControllerTest extends TestCase
{
    private function createTestClient(): string
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

        return $response->json('data.client_id');
    }

    public function test_check_credit_eligibility_successfully(): void
    {
        $clientId = $this->createTestClient();

        $requestData = [
            'client_id' => $clientId,
            'credit_id' => 'personal-loan',
        ];

        $response = $this->postJson('/api/v1/credits/check-eligibility', $requestData);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'eligible',
                    'reasons',
                    'final_credit',
                ],
            ]);

        // Verify that reasons array is not empty
        $this->assertNotEmpty($response->json('data.reasons'));
    }

    public function test_check_credit_eligibility_with_invalid_client(): void
    {
        $requestData = [
            'client_id' => '00000000-0000-0000-0000-000000000000',
            'credit_id' => 'personal-loan',
        ];

        $response = $this->postJson('/api/v1/credits/check-eligibility', $requestData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'success' => false,
                'message' => 'Client not found',
            ]);
    }

    public function test_check_credit_eligibility_with_invalid_credit(): void
    {
        $clientId = $this->createTestClient();

        $requestData = [
            'client_id' => $clientId,
            'credit_id' => 'non-existent-credit',
        ];

        $response = $this->postJson('/api/v1/credits/check-eligibility', $requestData);

        $response->assertStatus(Response::HTTP_BAD_REQUEST)
            ->assertJson([
                'success' => false,
                'message' => 'Credit product not found',
            ]);
    }

    public function test_process_credit_application_successfully(): void
    {
        $clientId = $this->createTestClient();

        $requestData = [
            'client_id' => $clientId,
            'credit_id' => 'personal-loan',
        ];

        $response = $this->postJson('/api/v1/credits/apply', $requestData);

        $response->assertSuccessful()
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'application_id',
                    'approved',
                    'reasons',
                    'final_credit',
                ],
            ]);

        // Verify application ID is UUID
        $applicationId = $response->json('data.application_id');
        $this->assertMatchesRegularExpression(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i',
            $applicationId
        );
    }

    public function test_process_credit_application_with_low_income_client(): void
    {
        // Create client with low income
        $clientData = [
            'name' => 'Poor Client',
            'age' => 30,
            'region' => 'PR',
            'income' => 500, // Below minimum requirement
            'score' => 600,
            'pin' => '987-65-4321',
            'email' => 'poor@example.com',
            'phone' => '+420987654321',
        ];

        $response = $this->postJson('/api/v1/clients', $clientData);
        $clientId = $response->json('data.client_id');

        $requestData = [
            'client_id' => $clientId,
            'credit_id' => 'personal-loan',
        ];

        $response = $this->postJson('/api/v1/credits/apply', $requestData);

        $response->assertSuccessful()
            ->assertJson([
                'success' => true,
                'data' => [
                    'approved' => false,
                ],
            ]);

        // Check that rejection reason is included
        $reasons = $response->json('data.reasons');
        $this->assertContains('Monthly income must be at least $1000', $reasons);
    }
}
