<?php

namespace App\Listeners;

use App\Domain\PushNotifications\Models\PushNotification;
use App\Domain\Users\Models\UserToken;
use App\Events\PushNotificationEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use RuntimeException;
use Throwable;

class PushNotificationListener implements ShouldQueue
{
    public $tries = 3;
    public $timeout = 20;
    public $queue = 'pushes';

    protected Messaging $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function handle(PushNotificationEvent $event)
    {
        $tokens = UserToken::query()
            ->where('user_id', $event->userId)
            ->whereNotNull('fcm_token')
            ->pluck('fcm_token')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (empty($tokens)) {
            $this->deleteHiddenNotification($event);

            return;
        }

        try {
            $message = CloudMessage::new()
                ->withNotification(Notification::create($event->notificationTitle, $event->notificationMessage))
                ->withData($this->buildDataPayload($event))
                ->withDefaultSounds();

            $report = $this->messaging->sendMulticast($message, $tokens);
        } catch (MessagingException | FirebaseException $exception) {
            throw new RuntimeException('Failed to send FCM notification: '.$exception->getMessage(), 0, $exception);
        }

        $invalidOrUnknownTokens = array_values(array_unique(array_merge(
            $report->invalidTokens(),
            $report->unknownTokens()
        )));

        if (!empty($invalidOrUnknownTokens)) {
            UserToken::query()
                ->whereIn('fcm_token', $invalidOrUnknownTokens)
                ->delete();
        }

        if ($report->hasFailures()) {
            $failureMessages = [];

            foreach ($report->failures()->getItems() as $failure) {
                $failureMessages[] = $failure->error()->getMessage();
            }

            \Log::warning('Firebase rejected one or more push targets.', [
                'notification_id' => $event->notificationId,
                'user_id' => $event->userId,
                'success_count' => $report->successes()->count(),
                'failure_count' => $report->failures()->count(),
                'invalid_tokens' => $report->invalidTokens(),
                'unknown_tokens' => $report->unknownTokens(),
                'errors' => array_slice($failureMessages, 0, 10),
            ]);
        }

        $this->deleteHiddenNotification($event);
    }

    public function failed(PushNotificationEvent $event, Throwable $exception)
    {
        $this->deleteHiddenNotification($event);

        \Log::error('Push notification delivery failed permanently.', [
            'notification_id' => $event->notificationId,
            'user_id' => $event->userId,
            'exception' => $exception->getMessage(),
        ]);
    }

    protected function deleteHiddenNotification(PushNotificationEvent $event)
    {
        if ((int) $event->hidden !== 1) {
            return;
        }

        PushNotification::query()
            ->whereKey($event->notificationId)
            ->where('hidden', 1)
            ->delete();
    }

    protected function buildDataPayload(PushNotificationEvent $event): array
    {
        $payload = [];

        foreach (array_merge($event->data, ['notification_id' => $event->notificationId]) as $key => $value) {
            $normalized = $this->normalizeDataValue($value);

            if ($normalized !== null) {
                $payload[(string) $key] = $normalized;
            }
        }

        if ($this->payloadSize($payload) > 3500 && array_key_exists('booking_info', $payload)) {
            unset($payload['booking_info']);
            $payload['booking_info_truncated'] = '1';
        }

        if ($this->payloadSize($payload) > 3500) {
            $allowedKeys = ['type', 'notification_id', 'booking_id', 'status', 'user_id'];
            $payload = array_intersect_key($payload, array_flip($allowedKeys));
            $payload['payload_truncated'] = '1';
        }

        return $payload;
    }

    protected function normalizeDataValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_bool($value)) {
            return $value ? '1' : '0';
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        if (is_array($value) || is_object($value)) {
            $json = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

            if ($json === false) {
                throw new RuntimeException('Failed to encode FCM data payload.');
            }

            return $json;
        }

        return null;
    }

    protected function payloadSize(array $payload): int
    {
        $json = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        if ($json === false) {
            throw new RuntimeException('Failed to measure FCM payload size.');
        }

        return strlen($json);
    }
}
