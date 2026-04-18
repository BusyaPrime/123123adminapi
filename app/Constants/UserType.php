<?php

declare(strict_types=1);

namespace App\Constants;

class UserType {
    const ANONYMOUS = 'anonymous';

    const REGISTERED = 'registered';

    public static function cases() {
        return [static::ANONYMOUS, static::REGISTERED];
    }
}

?>