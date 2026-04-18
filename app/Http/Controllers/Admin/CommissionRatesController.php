<?php

namespace App\Http\Controllers\Admin;

use App\Domain\CommissionRate\Jobs\StoreCommissionRateJob;
use App\Domain\CommissionRate\Jobs\UpdateCommissionRateJob;
use App\Domain\CommissionRate\Models\CommissionRate;
use App\Domain\CommissionRate\Requests\CommissionRequest;
use App\Http\Controllers\Controller;

class CommissionRatesController extends Controller
{
    public function index()
    {
        $rates = CommissionRate::paginate();
        return view('admin.commission-rates.index', [
            'rates' => $rates
        ]);
    }

    public function create()
    {
        return view('admin.commission-rates.create');
    }

    public function store(CommissionRequest $request)
    {
        try {
            $this->dispatchSync(new StoreCommissionRateJob($request));
            return redirect()->route('admin.commission-rates.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.commission-rates.index')->with('danger', trans('admin.store_failed'));
        }
    }

    public function edit(CommissionRate $rate)
    {
        return view('admin.commission-rates.edit', [
            'rate' => $rate
        ]);
    }

    public function update(CommissionRate $rate, CommissionRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateCommissionRateJob($request, $rate));
            return redirect()->route('admin.commission-rates.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.commission-rates.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(CommissionRate $rate)
    {
        try {
            $rate->delete();
            return redirect()->route('admin.commission-rates.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.commission-rates.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
