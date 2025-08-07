<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Client\Repositories\ClientRepositoryInterface;
use App\Domain\Credit\Repositories\CreditRepositoryInterface;
use App\Domain\Credit\Services\CreditApprovalService;
use App\Domain\Credit\Services\Rules\AgeRule;
use App\Domain\Credit\Services\Rules\CreditScoreRule;
use App\Domain\Credit\Services\Rules\IncomeRule;
use App\Domain\Credit\Services\Rules\OstravaRateAdjustmentRule;
use App\Domain\Credit\Services\Rules\PragueRandomRejectionRule;
use App\Domain\Credit\Services\Rules\RegionRule;
use App\Domain\CreditApplication\Repositories\CreditApplicationRepositoryInterface;
use App\Domain\Notification\Services\NotificationServiceInterface;
use App\Infrastructure\Notification\LogNotificationService;
use App\Infrastructure\Persistence\Json\JsonClientRepository;
use App\Infrastructure\Persistence\Json\JsonCreditApplicationRepository;
use App\Infrastructure\Persistence\Json\JsonCreditRepository;
use Illuminate\Support\ServiceProvider;

final class CreditSystemServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Repository bindings
        $this->app->singleton(ClientRepositoryInterface::class, JsonClientRepository::class);
        $this->app->singleton(CreditRepositoryInterface::class, JsonCreditRepository::class);
        $this->app->singleton(CreditApplicationRepositoryInterface::class, JsonCreditApplicationRepository::class);

        // Service bindings
        $this->app->singleton(NotificationServiceInterface::class, LogNotificationService::class);

        // Credit Approval Service with configurable rules
        $this->app->singleton(CreditApprovalService::class, function () {
            $service = new CreditApprovalService;

            // Add rules with configuration from .env
            $service->addRule(new AgeRule(
                config('credit.age.min'),
                config('credit.age.max')
            ));

            $service->addRule(new CreditScoreRule(
                config('credit.score.minimum')
            ));

            $service->addRule(new IncomeRule(
                config('credit.income.minimum')
            ));

            $service->addRule(new RegionRule);

            // Only add Prague rule if enabled
            if (config('credit.features.prague_random_rejection')) {
                $service->addRule(new PragueRandomRejectionRule);
            }

            $service->addRule(new OstravaRateAdjustmentRule);

            return $service;
        });
    }

    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../../config/credit.php' => config_path('credit.php'),
        ], 'credit-config');
    }
}
