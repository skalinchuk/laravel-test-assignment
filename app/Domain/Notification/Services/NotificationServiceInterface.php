<?php

declare(strict_types=1);

namespace App\Domain\Notification\Services;

use App\Domain\Client\Entities\Client;
use App\Domain\CreditApplication\Entities\CreditApplication;

interface NotificationServiceInterface
{
    public function notifyApproval(Client $client, CreditApplication $application): void;

    public function notifyRejection(Client $client, CreditApplication $application): void;
}
