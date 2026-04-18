<?php


namespace App\Http\Controllers\Admin;


use App\Domain\AdminMessages\Models\AdminMessage;
use App\Domain\Messages\Models\Message;
use App\Domain\PushNotifications\Jobs\SendPushJob;
use App\Events\ChatMessageSent;
use App\Domain\TruckBookings\Models\TruckBooking;
use App\Domain\Users\Filters\UserFilter;
use App\Domain\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'users');
        $lastSeenFormatted = null;
        if ($search) {
            if ($type == 'users') {
                $chats = User::where('role', '!=', User::ROLE_ADMIN)->with('profile')
                    ->whereHas('profile', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('surname', 'like', '%' . $search . '%')
                            ->orWhere('middle_name', 'like', '%' . $search . '%');
                    })
                    ->whereHas('adminMessages')
                    ->orderBy('last_chat_at', 'desc')
                    ->get();
            }
            if ($type == 'drivers') {
                $chats = User::whereHas('car')->with('profile')
                    ->whereHas('profile', function ($query) use ($search) {
                        $query->where('name', 'like', '%' . $search . '%')
                            ->orWhere('surname', 'like', '%' . $search . '%')
                            ->orWhere('middle_name', 'like', '%' . $search . '%');
                    })
                    ->whereHas('adminMessages')
                    ->orderBy('last_chat_at', 'desc')
                    ->get();
            }

        } else {
            if ($type == 'users') {
                $chats = User::where('role', '!=', User::ROLE_ADMIN)->with('profile')
                    ->whereHas('adminMessages')
                    ->orderBy('last_chat_at', 'desc')
                    ->get();
            }
            if ($type == 'drivers') {
                $chats = User::whereHas('car')->with('profile')
                ->whereHas('adminMessages')
                ->orderBy('last_chat_at', 'desc')
                ->get();
            }
        }
        
        $selectedChatId = $request->input('chat_id');
        $chatSelected = null;
        $selectedUser = null;
        if ($selectedChatId) {
            $chatSelected = AdminMessage::where('chat_id', $selectedChatId)->get();
            $selectedUser = User::find($selectedChatId);
        

            $today = date('d', time());
            $now = now();
            $yesterday = date('d', time() - 86400);
            $lastSeen = $selectedUser->profile->user->last_seen_at;
 
            if($now->format('H') == $lastSeen->format('H') && ($now->format('i') - 2) < $lastSeen->format('i')) $lastSeenFormatted = trans('admin.online');
            else if(!($now->format('H') == $lastSeen->format('H') && ($now->format('i') - 2) < $lastSeen->format('i')) && $selectedUser->profile->user->last_seen_at->format('d') == $today) $lastSeenFormatted = trans('admin.days.today').$lastSeen->format('H:i');
            else if($selectedUser->profile->user->last_seen_at->format('d') == $yesterday) $lastSeenFormatted = trans('admin.days.yesterday').$lastSeen->format('H:i');
            else if($selectedUser->profile->user->last_seen_at->format('d') < $yesterday) $lastSeenFormatted = $lastSeen->isoFormat('DD.MM ').trans('admin.days.at').$lastSeen->format('H:i');

            AdminMessage::where('chat_id', $selectedChatId)->where('user_id', '=', $selectedChatId)
            ->update(['read' => 1]);
        }

        return view('admin.chat.index', [
            'chats' => $chats,
            'selected_user' => $selectedUser,
            'selectedChatId' => $selectedChatId,
            'chatSelected' => $chatSelected,
            'search' => $search,
            'type' => $type,
            'last_seen' => $lastSeenFormatted
        ]);
    }

    public function sendMessage(Request $request)
    {
        $selectedChatId = $request->input('chat_id', null);
        $type = $request->input('type', 'users');
        $message = $request->input('message', null);
        if (!$selectedChatId || !$message) {
            return redirect()->back()->with('danger', 'Не верные данные');
        }
        $user = User::find($selectedChatId);
        if (!$user) {
            return redirect()->back()->with('danger', 'Не выбран пользователь');
        }
        $message = new AdminMessage([
            'chat_id' => $user->id,
            'user_id' => null,
            'message' => $request->input('message', null),
            'read' => 0
        ]);
        
        $message->save();
        $user->last_chat_at = now();
        $user->save();

        event(new ChatMessageSent($selectedChatId, $message));

        $data = ['type' => 'support'];

        $request->merge(['user_id' => $user->id]);
        $request->merge(['title' => trans('messages.push_messages.message')]);
        $request->merge(['message' => trans('messages.push_messages.message_text')]);
        $this->dispatchSync(new SendPushJob($request, 'users', false, $data));
        return redirect()->route('admin.chat.index', ['chat_id' => $selectedChatId, 'type' => $type]);
    }

    public function bookings(Request $request)
    {
        $search = $request->input('search');
        if ($search) {
            $chats = TruckBooking::whereHas('messages')
                ->where('id', $search)
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $chats = TruckBooking::whereHas('messages')
                ->orderBy('id', 'desc')
                ->get();
        }

        $selectedChatId = $request->input('chat_id');
        $chatSelected = null;
        $selectedBooking = null;
        if ($selectedChatId) {
            $selectedBooking = TruckBooking::find($selectedChatId);
            $chatSelected = $selectedBooking->messages()->get();
        }
        return view('admin.chat.index-bookings', [
            'chats' => $chats,
            'selected_booking' => $selectedBooking,
            'selectedChatId' => $selectedChatId,
            'chatSelected' => $chatSelected,
            'search' => $search,
        ]);
    }

    public function sendMessageBooking(Request $request)
    {
        $selectedChatId = $request->input('chat_id', null);
        $message = $request->input('message', null);
        if (!$selectedChatId || !$message) {
            return redirect()->back()->with('danger', 'Не верные данные');
        }
        $booking = TruckBooking::find($selectedChatId);
        if (!$booking) {
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

        return redirect()->route('admin.chat.index-bookings', ['chat_id' => $selectedChatId]);
    }
}
