<?php

namespace App\Events;

use DateTimeInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\JsonResource;
use JsonSerializable;

class PushNotificationEventApp implements ShouldBroadcast
{
    public $notification;
    public $data;
    public $user;

    protected $channelUserId;

    public function __construct($notification, $user, $data)
    {
        $this->channelUserId = (int) ($notification->user_id ?? $user->id);
        $this->notification = [
            'id' => (int) $notification->id,
            'user_id' => (int) $notification->user_id,
            'title' => $notification->title,
            'message' => $notification->message,
            'read' => (bool) ($notification->read ?? false),
            'hidden' => (int) ($notification->hidden ?? 0),
            'created_at' => optional($notification->created_at)->format('d.m.Y H:i:s'),
        ];
        $this->user = [
            'id' => (int) $user->id,
            'name' => $user->name,
            'username' => $user->username,
            'role' => $user->role,
        ];
        $this->data = $this->normalizePayload($data);
    }

    public function broadcastAs()
    {
        return 'PushNotification';
    }

    public function broadcastOn()
    {
        return new Channel('pushnotification.' . $this->channelUserId);
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
