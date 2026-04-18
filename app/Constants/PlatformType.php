<?php

declare(strict_types=1);

namespace App\Constants;

class PlatformType
{
    const WEB = 'web';

    const ANDROID = 'android';
    const IOS = 'ios';

    public static function cases()
    {
        return [static::WEB, static::ANDROID, static::IOS];
    }
}

?>