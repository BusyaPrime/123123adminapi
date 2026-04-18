<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Rap2hpoutre\LaravelLogViewer\LaravelLogViewer;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

use App\Domain\Users\Models\UserCode;

class SystemController extends Controller
{
    public function index(Request $request){}

    public function systemLogs(Request $request){
        $logViewer = new LaravelLogViewer();
        $logViewer->setFile('laravel.log');
        $logs = \File::exists(storage_path('logs/laravel.log')) ? $logViewer->all() : [];

        $page = $request->input('page', 1);
        $paginated = $this->paginate($logs, 15, $page, [
            'path' => route('admin.system.logs'),
            'query' => $request->all()
        ]);
        return view('admin.system.logs', ['logs' => $paginated]);
    }

    public function clearSystemLogs(Request $request){
        try {
            $message = \Artisan::call('logs:clear');
            return redirect()->back()->with('success', trans('Логи успешно очищены!'));
        } catch (\Throwable $th) {
            return redirect()->back()->with('danger', trans('Ошибка при очистки логов!'));
        }
    }

    public function confirmationCodes(Request $request){
        $codes = UserCode::orderByDesc('created_at')->paginate();
        return view('admin.system.confirmation-codes', [
            'codes' => $codes
        ]);
    }

    private function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
