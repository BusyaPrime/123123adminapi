<?php

namespace App\Domain\TruckBookings\Exceptions;

use RuntimeException;

class CompanyNotFoundForPaymentException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('company_not_found');
    }
}
