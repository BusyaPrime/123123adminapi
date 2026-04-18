<?php


namespace App\Domain\Tickets\Jobs;


use App\Domain\Tickets\Models\Ticket;
use App\Domain\Tickets\Requests\StoreTicketRequest;
use App\Domain\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreTicketJob
{
    use Queueable, Dispatchable;

    protected $request;

    public function __construct(StoreTicketRequest $request)
    {
        $this->request = $request;
    }


    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $ticket = new Ticket();

            $ticket->user_id = $this->request->input('user_id');
            $ticket->user_type = $this->request->input('user_type');
            if ($ticket->user_id && !$this->request->filled('user_name')) {
                $user = User::find($ticket->user_id);
                if ($user && $user->profile) {
                    $ticket->user_name = ($user->profile->name ?? '').' '.($user->profile->last_name ?? '');
                }
            } else {
                $ticket->user_name = $this->request->input('user_name');
            }
            $ticket->subject = $this->request->input('subject');
            $ticket->text = $this->request->input('text');
            $ticket->status = 'new';
            $ticket->admin_comment = null;

            if ($this->request->hasFile('file')) {
                $ticket->file = $ticket->uploadFile($this->request->file('file'));
            }

            $ticket->save();
            $ticket->refresh();

            //TODO: send email event

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $ticket;
    }
}
