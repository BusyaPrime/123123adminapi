<?php

declare(strict_types=1);

namespace App\Constants;

class TrackingSessionStatus
{
    const ACTIVE = 'active';

    const COMPLETED = 'completed';
    const ABANDONED = 'abandoned';

    public static function cases()
    {
        return [static::ACTIVE, static::COMPLETED, static::ABANDONED];
    }
}

?>