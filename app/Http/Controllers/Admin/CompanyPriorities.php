<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Domain\CompanyPriorities\Models\CompanyPriority;
use App\Domain\CompanyPriorities\Requests\CompanyPriorityRequest;
use App\Domain\CompanyPriorities\Jobs\StoreCompanyPriorityJob;

class CompanyPriorities extends Controller
{
    
    public function index()
    {
        $priorities = CompanyPriority::paginate();
        return view('admin.company-priorities.index', [
            'priorities' => $priorities
        ]);
    }

    public function create()
    {
        return view('admin.company-priorities.create');
    }

    public function store(CompanyPriorityRequest $request)
    {
        try {
            $this->dispatchSync(new StoreCompanyPriorityJob($request));
            return redirect()->route('admin.company_priorities.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            report($exception);
            return redirect()->route('admin.company_priorities.index')->with('danger', trans('admin.store_failed'));
        }
    }

    public function edit(CompanyPriority $priority)
    {
        return view('admin.company-priorities.edit', [
            'priority' => $priority
        ]);
    }

    public function update(CompanyPriority $priority, CompanyPriorityRequest $request)
    {
        try {
            $this->dispatchSync(new StoreCompanyPriorityJob($request, $priority));
            return redirect()->route('admin.company_priorities.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.company_priorities.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(CompanyPriority $priority)
    {
        try {
            $priority->delete();
            return redirect()->route('admin.company_priorities.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.company_priorities.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
