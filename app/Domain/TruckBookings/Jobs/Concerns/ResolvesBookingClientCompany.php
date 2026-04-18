<?php

namespace App\Domain\TruckBookings\Jobs\Concerns;

use App\Domain\TruckBookings\Models\TruckBooking;
use Illuminate\Validation\ValidationException;

trait ResolvesBookingClientCompany
{
    protected function resolveClientCompanyIdForPayment($authUser, string $paymentType): ?int
    {
        $companyId = $authUser ? $authUser->resolveClientCompanyId() : null;

        if ($paymentType === TruckBooking::PAY_COMPANY && $companyId === null) {
            throw ValidationException::withMessages([
                'payment_type' => 'company_not_found',
            ]);
        }

        return $companyId;
    }
}
