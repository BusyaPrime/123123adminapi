<?php

namespace App\Http\Controllers\Admin;

use App\Domain\CancelReasons\Models\CancelReason;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\CancelReasons\Models\CancelReasonTranslation;
use App\Domain\CancelReasons\Requests\CancelReasonRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CancelReasonsController extends Controller
{
    public function index()
    {
        $cancelReasons = CancelReason::paginate();

        return view('admin.cancel-reasons.index', [
            'cancel_reasons' => $cancelReasons,
        ]);
    }

    public function create()
    {
        return view('admin.cancel-reasons.create');
    }


    public function store(CancelReasonRequest $request)
    {
        try {
            $cancelReason = new CancelReason();
            $translations = [];
            foreach ($request->input('translations', []) as $translate) {
                if ($translate['reason'] == '') {
                    continue;
                }
                $translations[] = new CancelReasonTranslation($translate);
            }
            $cancelReason->save();
            if (!empty($translations)) {
                $cancelReason->translations()->saveMany($translations);
            }
            return redirect()->route('admin.cancel-reasons.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cancel-reasons.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(CancelReason $cancelReason)
    {
        return view('admin.cancel-reasons.edit', [
            'cancel_reason' => $cancelReason
        ]);
    }

    public function update(CancelReason $cancelReason, CancelReasonRequest $request)
    {
        try {
            $translations = [];
            $cancelReason->translations()->delete();
            foreach ($request->input('translations', []) as $translate) {
                if ($translate['reason'] == '') {
                    continue;
                }
                $translations[] = new CancelReasonTranslation($translate);
            }
            if (!empty($translations)) {
                $cancelReason->translations()->saveMany($translations);
            }
            return redirect()->route('admin.cancel-reasons.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cancel-reasons.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(CancelReason $cancelReason)
    {
        try {
            $cancelReasonID = $cancelReason->id;
            $cancelReason->delete();
            TruckBooking::where('cancel_reason_id', $cancelReasonID)
                ->update(['cancel_reason_id' => null]);
            return redirect()->route('admin.cancel-reasons.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.cancel-reasons.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
