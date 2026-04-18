<?php

namespace App\Events;

use App\Domain\Users\Models\User;
use DateTimeInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PushNotificationEvent
{
    use Dispatchable;

    public $notificationId;
    public $notificationUserId;
    public $notificationTitle;
    public $notificationMessage;
    public $hidden;
    public $userId;
    public $data;

    public function __construct($notification, User $user, $data = [])
    {
        $this->notificationId = (int) $notification->id;
        $this->notificationUserId = (int) $notification->user_id;
        $this->notificationTitle = (string) $notification->title;
        $this->notificationMessage = (string) $notification->message;
        $this->hidden = (int) $notification->hidden;
        $this->userId = (int) $user->id;
        $this->data = $this->normalizePayload($data);
    }

    protected function normalizePayload($value)
    {
        if ($value instanceof JsonResource) {
            return $this->normalizePayload($value->resolve());
        }

        if ($value instanceof Arrayable) {
            return $this->normalizePayload($value->toArray());
        }

        if ($value instanceof JsonSerializable) {
            return $this->normalizePayload($value->jsonSerialize());
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DateTimeInterface::ATOM);
        }

        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->normalizePayload($item);
            }

            return $value;
        }

        if (is_scalar($value) || $value === null) {
            return $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return null;
    }
}
