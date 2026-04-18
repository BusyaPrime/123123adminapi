<?php

declare(strict_types=1);

namespace App\Constants;

class TrackingOrderOutcome
{
    const IN_PROGRESS = 'in_progress';

    const COMPLETED = 'completed';
    const ABANDONED = 'abandoned';

    public static function cases()
    {
        return [static::IN_PROGRESS, static::COMPLETED, static::ABANDONED];
    }
}

?>