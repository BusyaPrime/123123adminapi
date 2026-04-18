<?php

namespace App\Http\Controllers\Admin;

use App\Domain\TicketThemes\Filters\TicketThemeFilter;
use App\Domain\TicketThemes\Jobs\StoreTicketThemeJob;
use App\Domain\TicketThemes\Jobs\UpdateTicketThemeJob;
use App\Domain\TicketThemes\Models\TicketTheme;
use App\Domain\TicketThemes\Requests\TicketThemeRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketThemesController extends Controller
{
    public function index(Request $request)
    {
        $filter = new TicketThemeFilter($request);
        $ticketThemes = TicketTheme::withTranslation()->filter($filter)->paginateFilter();

        return view('admin.ticket-themes.index', [
            'ticket_themes' => $ticketThemes,
            'filters' => $filter->filters(),
        ]);
    }

    public function create()
    {
        return view('admin.ticket-themes.create');
    }


    public function store(TicketThemeRequest $request)
    {
        try {
            $this->dispatchSync(new StoreTicketThemeJob($request));
            return redirect()->route('admin.ticket-themes.index')->with('success', trans('admin.store_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.ticket-themes.index')->with('danger', trans('admin.store_failed'));
        }

    }

    public function edit(TicketTheme $ticketTheme)
    {
        return view('admin.ticket-themes.edit', [
            'ticket_theme' => $ticketTheme
        ]);
    }

    public function update(TicketTheme $ticketTheme, TicketThemeRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateTicketThemeJob($ticketTheme, $request));
            return redirect()->route('admin.ticket-themes.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.ticket-themes.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(TicketTheme $ticketTheme)
    {
        try {
            $ticketTheme->delete();
            return redirect()->route('admin.ticket-themes.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.ticket-themes.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
