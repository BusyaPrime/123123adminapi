<?php

namespace App\Domain\TicketThemes\Jobs;

use App\Domain\TicketThemes\Models\TicketTheme;
use App\Domain\TicketThemes\Models\TicketThemeTranslation;
use App\Domain\TicketThemes\Requests\TicketThemeRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;

class StoreTicketThemeJob
{

    use Dispatchable, Queueable;

    protected $request;

    public function __construct(TicketThemeRequest $request)
    {
        $this->request = $request;
    }


    /**
     * @return TicketTheme
     * @throws \Exception
     */
    public function handle()
    {

        \DB::beginTransaction();
        try {
            $ticketTheme = new TicketTheme();

            $ticketTheme->priority = $this->request->input('priority', 0);
            $ticketTheme->save();

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

            $ticketTheme->save();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $ticketTheme;
    }
}
