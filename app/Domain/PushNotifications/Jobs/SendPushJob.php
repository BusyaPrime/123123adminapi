<?php


namespace App\Domain\PushNotifications\Jobs;


use App\Domain\PushNotifications\Models\PushNotification;
use App\Domain\Users\Models\User;
use App\Events\PushNotificationEvent;
use App\Events\PushNotificationEventApp;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;

class SendPushJob
{
    use Dispatchable, Queueable;

    protected $request;
    protected $type;
    protected $needSave;
    protected $data;

    public function __construct(Request $request, $type = 'users', $needSave = true, $data = [])
    {
        $this->request = $request;
        $this->type = $type;
        $this->needSave = $needSave;
        $this->data = $data;
    }


    /**
     * @throws \Exception
     */
    public function handle()
    {
        \DB::beginTransaction();
        try {
            if($this->request->filled('user_id')) {
                $users = User::where('id', $this->request->input('user_id'))->get();
            } else {
                if($this->type == 'users') {
                    $users = User::with('profile')->where('role', '<>', User::ROLE_ADMIN)->whereDoesntHave('car')->get();
                } else {
                    if($this->request->filled('company_id')) {
                        $companyId = $this->request->input('company_id');
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

            foreach ($users as $user) {
                $notification = new PushNotification();
                $notification->user_id = $user->id;
                
                if(gettype($this->request->input('title')) == 'array') {
                    $title = $this->request->input('title', ['message' => config('app.name'), 'arg' => '']);
                    $notification->title = trans($title['message'], ['arg' => $title['arg']], $user->profile->language ?? \App::getLocale());
                } else {
                    $notification->title = trans($this->request->input('title', config('app.name')), [], $user->profile->language ?? \App::getLocale());
                }

                if(gettype($this->request->input('message')) == 'array') {
                    $body = $this->request->input('message', ['message' => trans('messages.Новое уведомление'), 'arg' => '']);
                    $notification->message = trans($body['message'], ['arg' => $body['arg']], $user->profile->language ?? \App::getLocale());
                } else {
                    $notification->message = trans($this->request->input('message', trans('messages.Новое уведомление')), [], $user->profile->language ?? \App::getLocale());
                }

                if($this->needSave) {
                    $notification->hidden = 0;
                    $data = ['type' => $this->request->input('notification_type', 'notification'), 'notification_id' => $notification->user_id];
                    $this->data = array_merge($data, $this->data ?? []);
                } else {
                    $notification->hidden = 1;
                }
                
                $notification->save();
                event(new PushNotificationEvent($notification, $user, $this->data));
                event(new PushNotificationEventApp($notification, $user, $this->data));
            }

        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }
        \DB::commit();

        return $notification;
    }
}
