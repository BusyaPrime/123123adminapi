<?php

namespace App\Domain\Contacts\Jobs;

use App\Domain\Contacts\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class UpdateContactJob
{
    use Dispatchable, Queueable;

    /**
     * @var Request
     */
    private $request;

    /**
     * StoreUserJob constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            $keysAvailable = Contact::$keysAvailable;

            Contact::truncate();

            foreach ($keysAvailable as $item) {
                Contact::create([
                    'key' => $item,
                    'value' => $this->request->input($item, null)
                ]);
            }
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();
    }
}
