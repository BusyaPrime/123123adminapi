<?php

namespace App\Domain\Tickets\Jobs;

use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Requests\UpdateTicketRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateTicketJob
{

    use Dispatchable, Queueable;


    public $ticket;

    public $request;

    public function __construct(Ticket $ticket, UpdateTicketRequest $request)
    {
        $this->request = $request;
        $this->ticket = $ticket;
    }


    /**
     * Execute the job.
     *
     * @return Ticket
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $ticket = $this->ticket;

            $ticket->status = $this->request->input('status', 'new');
            $ticket->admin_comment = $this->request->input('admin_comment');
            $ticket->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $ticket;
    }
}
