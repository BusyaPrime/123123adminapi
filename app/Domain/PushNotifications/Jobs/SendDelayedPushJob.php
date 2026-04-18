<?php

namespace App\Domain\PushNotifications\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Domain\Users\Models\User;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\PushNotifications\Models\PushNotification;
use App\Events\PushNotificationEvent;


class SendDelayedPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $request;

    protected $type;
    protected $needSave;
    protected $data;



    public function __construct($request, $type = 'users', $needSave = true, $data = [])
    {
        $this->request = $request;
        $this->type = $type;
        $this->needSave = $needSave;
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            if(isset($this->data) && isset($this->data['booking_id'])){
                $booking = TruckBooking::find($this->data['booking_id']);
                if(!$booking || $booking->status == TruckBooking::STATUS_DONE) return;
            }

            if($this->request['user_id']) {
                $users = User::where('id', $this->request['user_id'])->get();
            } else {
                if($this->type == 'users') {
                    $users = User::with('profile')->where('role', '<>', User::ROLE_ADMIN)->whereDoesntHave('car')->get();
                } else {
                    if($this->request['company_id']) {
                        $companyId = $this->request['company_id'];
                        $users = User::whereHas('car', function (Builder $query) use ($companyId) {
                            $query->whereHas('companies', function (Builder $query) use ($companyId) {
                                $query->where('companies.id', $companyId);
                            });
                        })->get();
                    } else {
                        $users = User::whereHas('car')->get();
                    }
                }
            }

            $notification = new PushNotification();

            foreach ($users as $user) {
                $notification = new PushNotification();
                $notification->user_id = $user->id;
                
                if(gettype($this->request['title']) == 'array') {
                    $title = $this->request['title'] ?? ['message' => config('app.name'), 'arg' => ''];
                    $notification->title = trans($title['message'], ['arg' => $title['arg']], $user->profile->language ?? \App::getLocale());
                }
                else $notification->title = trans($this->request['title'] ?? config('app.name'), [], $user->profile->language ?? \App::getLocale());

                if(gettype($this->request['message']) == 'array') {
                    $body = $this->request['message'] ?? ['message' => trans('messages.Новое уведомление'), 'arg' => ''];
                    $notification->message = trans($body['message'], ['arg' => $body['arg']], $user->profile->language ?? \App::getLocale());
                }
                else $notification->message = trans($this->request['message'] ?? trans('messages.Новое уведомление'), [], $user->profile->language ?? \App::getLocale());

                $notification->save();
                if($this->needSave) {
                    $notification->hidden = 0;
                    $data = ['type' => 'notification', 'notification_id' => $notification->user_id];
                    $this->data = array_merge($data, $this->data ?? []);
                } else {
                    $notification->hidden = 1;
                }
                
                event(new PushNotificationEvent($notification, $user, $this->data));
            }

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();
    }
}
