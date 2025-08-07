<?php

declare(strict_types=1);

namespace Tests;

use App\Providers\CreditSystemServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp(): void
    {
        parent::setUp();

        // Ensure the service provider is loaded
        $this->app->register(CreditSystemServiceProvider::class);

        // Clear JSON files before each test
        $this->clearJsonStorage();
    }

    protected function tearDown(): void
    {
        // Clear JSON files after each test
        $this->clearJsonStorage();

        parent::tearDown();
    }

    private function clearJsonStorage(): void
    {
        $files = [
            'private/clients.json',
            'private/credits.json',
            'private/credit_applications.json',
        ];

        foreach ($files as $file) {
            $filePath = storage_path('app/'.$file);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }
    }
}
