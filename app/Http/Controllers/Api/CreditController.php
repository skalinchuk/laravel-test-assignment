<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Credit\UseCases\CheckCreditEligibilityCommand;
use App\Application\Credit\UseCases\CheckCreditEligibilityUseCase;
use App\Application\Credit\UseCases\GetAvailableCreditsUseCase;
use App\Application\Credit\UseCases\ProcessCreditApplicationCommand;
use App\Application\Credit\UseCases\ProcessCreditApplicationUseCase;
use App\Application\CreditApplication\UseCases\GetClientApplicationsCommand;
use App\Application\CreditApplication\UseCases\GetClientApplicationsUseCase;
use App\Application\CreditApplication\UseCases\GetCreditApplicationCommand;
use App\Application\CreditApplication\UseCases\GetCreditApplicationUseCase;
use App\Http\Requests\CheckCreditEligibilityRequest;
use App\Http\Requests\ProcessCreditApplicationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use InvalidArgumentException;

final class CreditController extends Controller
{
    public function __construct(
        private readonly CheckCreditEligibilityUseCase $checkEligibilityUseCase,
        private readonly ProcessCreditApplicationUseCase $processApplicationUseCase,
        private readonly GetCreditApplicationUseCase $getCreditApplicationUseCase,
        private readonly GetAvailableCreditsUseCase $getAvailableCreditsUseCase,
        private readonly GetClientApplicationsUseCase $getClientApplicationsUseCase,
    ) {}

    public function index(): JsonResponse
    {
        try {
            $response = $this->getAvailableCreditsUseCase->execute();

            return response()->json([
                'success' => true,
                'data' => [
                    'credits' => $response->credits,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve available credits',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function checkEligibility(CheckCreditEligibilityRequest $request): JsonResponse
    {
        try {
            $command = new CheckCreditEligibilityCommand(
                clientId: $request->validated('client_id'),
                creditId: $request->validated('credit_id'),
            );

            $response = $this->checkEligibilityUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'eligible' => $response->isEligible,
                    'reasons' => $response->reasons,
                    'final_credit' => $response->finalCredit ? [
                        'id' => $response->finalCredit->getId(),
                        'name' => $response->finalCredit->getName(),
                        'amount' => $response->finalCredit->getAmount()->getAmount(),
                        'rate' => $response->finalCredit->getRate()->getValue(),
                    ] : null,
                ],
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function processApplication(ProcessCreditApplicationRequest $request): JsonResponse
    {
        try {
            $command = new ProcessCreditApplicationCommand(
                clientId: $request->validated('client_id'),
                creditId: $request->validated('credit_id'),
            );

            $response = $this->processApplicationUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'application_id' => $response->applicationId,
                    'approved' => $response->approved,
                    'reasons' => $response->reasons,
                    'final_credit' => $response->finalCredit ? [
                        'id' => $response->finalCredit->getId(),
                        'name' => $response->finalCredit->getName(),
                        'amount' => $response->finalCredit->getAmount()->getAmount(),
                        'rate' => $response->finalCredit->getRate()->getValue(),
                    ] : null,
                ],
            ], $response->approved ? Response::HTTP_CREATED : Response::HTTP_OK);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_BAD_REQUEST);
        }
    }

    public function showApplication(string $applicationId): JsonResponse
    {
        try {
            $command = new GetCreditApplicationCommand($applicationId);
            $response = $this->getCreditApplicationUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $response->id,
                    'client_id' => $response->clientId,
                    'credit_id' => $response->creditId,
                    'status' => $response->status,
                    'applied_at' => $response->appliedAt,
                    'processed_at' => $response->processedAt,
                    'reasons' => $response->reasons,
                    'final_credit' => $response->finalCredit,
                ],
            ]);

        } catch (InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], Response::HTTP_NOT_FOUND);
        }
    }

    public function getClientApplications(string $clientId): JsonResponse
    {
        try {
            $command = new GetClientApplicationsCommand($clientId);
            $response = $this->getClientApplicationsUseCase->execute($command);

            return response()->json([
                'success' => true,
                'data' => [
                    'applications' => $response->applications,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve client applications',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
