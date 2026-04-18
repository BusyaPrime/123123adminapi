<?php

namespace App\Listeners;

use App\Domain\Cars\Models\Car;
use App\Domain\Tcars\Models\Tcar;
use App\Domain\TruckBookings\Resources\TruckBookingResource;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Events\SearchEvent;
use App\Events\TcarSearchEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
// use App\Events\OrderStatusEvent;
// use Carbon\Carbon;
// use \FCM;
// use LaravelFCM\Message\OptionsBuilder;
// use LaravelFCM\Message\PayloadDataBuilder;
// use LaravelFCM\Message\PayloadNotificationBuilder;

class SearchStartNotification implements ShouldQueue
{
    public function handle($event)
    {
        try {
            $message = '';
            $tgMessage = '<b>⌛️ Новый заказ #' . $event->booking->id . "</b>\n\n";
            $_routes = json_decode($event->booking->routes, true);

            foreach ($_routes as $i => $route) {
                if ($i == 0) {
                    $icon = "📤 Адрес отправки: ";
                }

                if (count($_routes) > 2 && $i == 1) {
                    $icon = "🏁 Адрес доставки: ";
                }

                if (($i + 1) == count($_routes)) {
                    $icon = "🏁 Адрес доставки: ";
                }
                $message .= $icon . $route['address'] . "\n" ?? '';
            }

            $message .= "---------------------------";

            $message .= "\nИмя отправителя: <b>" . $event->booking->user->surname . " " . $event->booking->user->name . "</b>\n\n";


            $message .= "\nВремя заявки: <b>" . $event->booking->created_at->format('d.m.Y H:i') . "</b>\n";
            if ($event->booking->is_round_trip > 0)
                $message .= "Тип заказа: <b>Туда и обратно</b>\n";
            if ($event->booking->partial_percentage > 0)
                $message .= "Процент погрузки: <b>" . $event->booking->partial_percentage . "</b>\n";
            $message .= "\nДата и время погрузки: <b>" . $event->booking->pickup_date . " " . $event->booking->pickup_time . "</b>";
            $message .= "\nТип оплаты: <b>" . __('admin.payment_types.' . $event->booking->payment_type) . "</b>";

            $comment = $event->booking->comment ?? 'Нет комментарий';

            $message .= "\n---------------------------\n";
            $message .= "\nКомментарий к заказу: " . "<b>" . $comment . "</b>\n";
            $message .= "\n---------------------------\n";

            $message .= "\nЦена: <b>" . number_format($event->booking->price, 0, ".", " ") . " сум.</b>";

            $tgMessage .= $message;

            if ($event->booking->carType) {
                $tgMessage .= "\nТариф: " . $event->booking->carType->title;
            }

            $botId = 'bot5335209292:AAGW9F99CKlnjh2yagMliVtBvDGKdoC2OLY';
            $chatId = '-1001369936893';

            $tgMessage .= "\n\n<a href='" . route('admin.bookings.show', ['booking' => $event->booking->id]) . "'>Нажмите для просмотра</a>";
            // @file_get_contents('https://api.telegram.org/' . $botId . '/sendMessage?chat_id=' . $chatId . '&parse_mode=HTML&text=' . urlencode($tgMessage));

            $booking = TruckBooking::withCarType()
                ->withCargoType()
                ->withLoadType()
                ->with('user', 'driver')->find($event->booking->id);
            // $bookingRoutes = json_decode($booking->routes, true);


            $cars = $event->booking->cars;
            $bookingResource = TruckBookingResource::make($booking);

            foreach ($cars as $car) {
                // foreach ($car->user->tokens as $token) {
                //     $fcm_token = $token->fcm_token;
                //     if($fcm_token) {
                //         // $downstreamResponse = FCM::sendTo($fcm_token, $option, $notification, $data);
                //         // $t = $downstreamResponse->numberSuccess();
                //     }
                // }

                if ($car instanceof Car) {
                    event(new SearchEvent($bookingResource, $car->id));
                } else if ($car instanceof Tcar) {
                    event(new TcarSearchEvent(['id' => $event->booking->id], $car->id));
                }
                //         }
            }
        } catch (\Exception $err) {
            \Log::info($err);
        }
    }
}
