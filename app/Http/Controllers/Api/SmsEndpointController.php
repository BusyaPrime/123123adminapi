<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\SmsService\SmsService;
use Illuminate\Http\Request;

class SmsEndpointController extends Controller
{
    public function send(Request $request)
    {
        // $number = $request->input('messages.0.recipient', '998946661903');
        // $message = $request->input('messages.0.sms.content.text', 'Test');
        // $smsService = new SmsService();
        // $smsService->send($number, $message);
        // return response()->json(null);
    }
}
