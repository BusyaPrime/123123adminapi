<?php

namespace App\Http\Controllers;

use App\Domain\OctoTransactions\Models\OctoTransaction;
use App\Domain\Transactions\Models\Transaction;
use App\Domain\Users\Models\User;
use Illuminate\Http\Request;

class OctoController extends Controller
{

    public function success(Request $request)
    {
        return view('site.octo-return');
    }
    
    public function notify(Request $request)
    {
        $shop_transaction_id = $request->input('shop_transaction_id');
        $octo_payment_id = $request->input('octo_payment_UUID');

        $status = $request->input('status');
        $total_sum = $request->input('total_sum');

        $signature = $request->input('signature');
        $hash_key = $request->input('hash_key');
        //$transfer_sum = $request->input('transfer_sum');
        //$refunded_sum = $request->input('refunded_sum');
        //$card_country = $request->input('card_country');
        //$maskedPan = $request->input('maskedPan');
        //$riskLevel = $request->input('riskLevel');
        //$payed_time = $request->input('payed_time');

        //@file_get_contents("https://api.telegram.org/bot823114266:AAEBlQ0xj_G7-l6Dn7IpwIofNz35IUxRkiI/sendMessage?chat_id=471242650&parse_mode=HTML&text=".urlencode($tgMessage));
        //@file_get_contents("https://api.telegram.org/bot823114266:AAEBlQ0xj_G7-l6Dn7IpwIofNz35IUxRkiI/sendMessage?chat_id=471242650&parse_mode=HTML&text=".urlencode('asd'));
        $transcation = OctoTransaction::find($shop_transaction_id);

        if ($transcation && $total_sum == $transcation->total_sum && $status == "succeeded") {
            $transcation->status = $status ?? $transcation->status;
            $transcation->save();

            $userId = $transcation->user_id;
            $user = User::find($userId);
            if ($user) {
                $transaction = new Transaction();
                $transaction->user_id = $user->id;
                $transaction->company_id = null;
                $transaction->type = $request->input('type', 'refill');
                $transaction->description = "Пополнение баланса через OCTO пользователя ID $userId";
                $transaction->amount = $total_sum;
                if ($transaction->save() && $user->profile) {
                    if ($transaction->type == 'refill') {
                        $user->profile->balance = (int)$user->profile->balance + $transaction->amount;
                    } else {
                        $user->profile->balance = (int)$user->profile->balance - $transaction->amount;
                    }
                    $user->profile->save();
                }
            }

        }
        return response()->json();
    }

    public function offer(){
        return view('documents.offer');
    }

    public function index() {
        return view('welcome');
    }
}
