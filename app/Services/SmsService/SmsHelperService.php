<?php

namespace App\Services\SmsService;

class SmsHelperService
{
    /**
     * Удаляет из номера телефона все символы кроме цифр.
     *
     * @param string $phone
     * @return string
     */
    public static function clearPhone($phone)
    {
        return preg_replace('/[^0-9]/', '', $phone);
    }

    /**
     * Проверяет является ли номер узбекским.
     *
     * @param string $phone
     * @return bool
     */
    public static function isUzPhone($phone)
    {
        return preg_match('/^998[0-9]{9}$/', $phone) == 1 ? true : false;
    }
}

?>