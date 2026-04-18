<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Tickets\Filters\TicketFilter;
use App\Domain\Tickets\Jobs\UpdateTicketJob;
use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Requests\UpdateTicketRequest;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketsController extends Controller
{
    public function index(Request $request)
    {
        $filter = new TicketFilter($request);
        $tickets = Ticket::filter($filter)->paginateFilter();

        return view('admin.tickets.index', [
            'tickets' => $tickets,
            'filters' => $filter->filters(),
        ]);
    }

    public function edit(Ticket $ticket)
    {
        return view('admin.tickets.edit', [
            'ticket' => $ticket
        ]);
    }

    public function update(Ticket $ticket, UpdateTicketRequest $request)
    {
        try {
            $this->dispatchSync(new UpdateTicketJob($ticket, $request));
            return redirect()->route('admin.tickets.index')->with('success', trans('admin.update_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.tickets.index')->with('danger', trans('admin.update_failed'));
        }
    }

    public function destroy(Ticket $ticket)
    {
        try {
            $ticket->delete();
            return redirect()->route('admin.tickets.index')->with('success', trans('admin.destroy_success'));
        } catch (\Exception $exception) {
            return redirect()->route('admin.tickets.index')->with('danger', trans('admin.destroy_failed'));
        }
    }
}
