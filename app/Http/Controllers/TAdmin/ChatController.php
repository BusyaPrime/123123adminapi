<?php

namespace App\Http\Controllers\TAdmin;

use App\Domain\Messages\Models\Message;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function bookings(Request $request)
    {

        $company = $request->input('_company');
        $request->merge(['company_id' => $company->id]);

        $search = $request->input('search');
        if ($search) {
            $chats = TruckBooking::whereHas('messages')
                ->where('id', $search)
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $chats = TruckBooking::whereHas('messages')
                ->where('company_id', $company->id)
                ->orderBy('id', 'desc')
                ->get();
        }

        $selectedChatId = $request->input('chat_id');
        $chatSelected = null;
        $selectedBooking = null;
        if ($selectedChatId) {
            $selectedBooking = TruckBooking::where('company_id', $company->id)->find($selectedChatId);
            $chatSelected = $selectedBooking->messages()->get();
        }
        return view('admin2.chat.index-bookings', [
            'chats' => $chats,
            'selected_booking' => $selectedBooking,
            'selectedChatId' => $selectedChatId,
            'chatSelected' => $chatSelected,
            'search' => $search,
        ]);
    }

    public function sendMessageBooking(Request $request)
    {
        $company = $request->input('_company');

        $selectedChatId = $request->input('chat_id', null);
        $message = $request->input('message', null);

        if (!$selectedChatId || !$message) {
            return redirect()->back()->with('danger', 'Не верные данные');
        }
        $booking = TruckBooking::find($selectedChatId);
        if (!$booking) {
            return redirect()->back()->with('danger', 'Не выбран заказ');
        }
        if ($booking->company_id != $company->id) {
            return redirect()->back()->with('danger', 'Не выбран заказ');
        }
        $message = new Message([
            'truck_booking_id' => $booking->id,
            'user_id' => $request->user()->id,
            'message' => $request->input('message', null),
            'read' => 0
        ]);
        $message->save();

        if($booking->user) {
            $data = [];
            $request->merge(['user_id' => $booking->user->user_id]);
            $request->merge(['title' => trans('admin.push_messages.booking_message')]);
            $request->merge(['message' => trans('admin.push_messages.booking_text').' №'.$booking->id]);
            $this->dispatchSync(new SendPushJob($request, 'users', false, $data));
        }

        if($booking->driver) {
            $data = [];
            $request->merge(['user_id' => $booking->driver->user_id]);
            $request->merge(['title' => trans('admin.push_messages.booking_message')]);
            $request->merge(['message' => trans('admin.push_messages.booking_text').' №'.$booking->id]);
            $this->dispatchSync(new SendPushJob($request, 'drivers', false, $data));
        }

        return redirect()->route('admin2.chat.index-bookings', ['chat_id' => $selectedChatId]);
    }
}
