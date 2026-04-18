<?php


namespace App\Http\Controllers\Api;


use App\Domain\AdminMessages\Models\AdminMessage;
use App\Domain\AdminMessages\Requests\AdminMessageRequest;
use App\Domain\AdminMessages\Resources\AdminMessageResource;
use App\Http\Controllers\Controller;
use App\Events\ChatMessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminMessageController extends Controller
{

    public function messages(Request $request)
    {
        $userId = $request->input('authUser')->id;
        $messages = AdminMessage::where('chat_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
        AdminMessage::where('chat_id', $userId)->whereNull('user_id')
            ->update(['read' => 1]);
        return AdminMessageResource::collection($messages);
    }
    public function messagesCount(Request $request)
    {
        $userId = $request->input('authUser')->id;
        $count = AdminMessage::where('chat_id', $userId)->whereNull('user_id')->where('read', 0)->count();
//        AdminMessage::where('chat_id', $userId)->whereNull('user_id')
//            ->update(['read' => 1]);
        return response()->json(['data' => ['count' => $count ?? 0]]);
    }

    public function sendMessage(AdminMessageRequest $request)
    {
        try {
            $user = $request->input('authUser');
            $message = new AdminMessage([
                'chat_id' => $user->id,
                'user_id' => $user->id,
                'message' => $request->input('message', null),
                'read' => 0
            ]);
            $message->save();
            $user->last_chat_at = now();
            $user->save();
            try {
                event(new ChatMessageSent($user->id, $message));
            } catch (\Throwable $eventException) {
                Log::warning('Admin support message notification failed', [
                    'chat_id' => $user->id,
                    'message_id' => $message->id,
                    'error' => $eventException->getMessage(),
                ]);
            }
            return AdminMessageResource::make($message->load('user'));
        } catch (\Throwable $exception) {
            Log::error('Admin support message send failed', [
                'error' => $exception->getMessage(),
            ]);
            return response()->json(['message' => $exception->getMessage()], 500);
        }
    }
}
