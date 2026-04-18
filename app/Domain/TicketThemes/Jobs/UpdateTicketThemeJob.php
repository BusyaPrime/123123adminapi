<?php

namespace App\Domain\TicketThemes\Jobs;

use App\Domain\TicketThemes\Models\TicketTheme;
use App\Domain\TicketThemes\Models\TicketThemeTranslation;
use App\Domain\TicketThemes\Requests\TicketThemeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateTicketThemeJob
{

    use Dispatchable, Queueable;


    public $ticketTheme;

    public $request;

    public function __construct(TicketTheme $ticketTheme, TicketThemeRequest $request)
    {
        $this->request = $request;
        $this->ticketTheme = $ticketTheme;
    }


    /**
     * Execute the job.
     *
     * @return TicketTheme
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $ticketTheme = $this->ticketTheme;

            $ticketTheme->priority = $this->request->input('priority', 0);
            $ticketTheme->save();

            $ticketTheme->translations()->delete();
            $translations = [];
            foreach ($this->request->input('translations', []) as $translate) {
                if ($translate['title'] == '') {
                    continue;
                }
                $translations[] = new TicketThemeTranslation($translate);
            }

            if (!empty($translations)) {
                $ticketTheme->translations()->saveMany($translations);
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $ticketTheme;
    }
}
