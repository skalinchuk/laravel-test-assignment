<?php

declare(strict_types=1);

namespace App\Infrastructure\Notification;

use App\Domain\Client\Entities\Client;
use App\Domain\CreditApplication\Entities\CreditApplication;
use App\Domain\Notification\Services\NotificationServiceInterface;
use Illuminate\Support\Facades\Log;

final class LogNotificationService implements NotificationServiceInterface
{
    public function notifyApproval(Client $client, CreditApplication $application): void
    {
        if (! config('credit.features.notifications_enabled', true)) {
            return;
        }

        $message = sprintf(
            'Уведомление клиенту %s: Кредит одобрен. ID заявки: %s',
            $client->getName(),
            $application->getId()
        );

        Log::info($message);
    }

    public function notifyRejection(Client $client, CreditApplication $application): void
    {
        if (! config('credit.features.notifications_enabled', true)) {
            return;
        }

        $reasons = implode(', ', $application->getReasons());
        $message = sprintf(
            'Уведомление клиенту %s: Кредит отклонен. Причины: %s. ID заявки: %s',
            $client->getName(),
            $reasons,
            $application->getId()
        );

        Log::info($message);
    }
}
