<?php

namespace App\Services\OctoService;

use App\Domain\OctoTransactions\Models\OctoTransaction;

class OctoService
{

    protected $url = 'https://secure.octo.uz';
    protected $shopId;
    protected $secret;
    protected $notifyUrl = 'https://casva.uz/octo/notify';
    public $returnUrl = 'https://casva.uz/octo/success';

    public function __construct()
    {
        $this->shopId = env('OCTO_SHOP_ID');
        $this->secret = env('OCTO_SECRET');
    }

    public function prepare($userId, $requestData)
    {
        $transaction = new OctoTransaction();
        $transaction->user_id = $userId;
        $transaction->total_sum = round($requestData['total_sum'] ?? 0);
        $lang = strtolower(app()->getLocale());
        if (!in_array($lang, ['ru', 'uz', 'oz', 'en'])) {
            $lang = 'ru';
        }

        if ($transaction->save()) {
            $data = [
                'octo_shop_id' => $this->shopId,
                'octo_secret' => $this->secret,
                'shop_transaction_id' => $transaction->id,
                'auto_capture' => true,
                'test' => false,
                'init_time' => $transaction->created_at ? $transaction->created_at->format('Y-m-d H:i:s') : date('Y-m-d H:i:s'),
                'total_sum' => $transaction->total_sum,
                'currency' => 'UZS',
                'description' => trans('admin.octo_messages.description') . $transaction->user_id,
                'language' => $lang,
                'return_url' => $this->returnUrl,
                'notify_url' => $this->notifyUrl,
                'ttl' => 15
            ];

            $url = $this->url . '/prepare_payment';
            $ch = curl_init($url);
            $payload = json_encode($data);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            $err = curl_error($ch);

            curl_close($ch);

            if ($response) {
                $responseData = json_decode($response, true);

                $transaction->error = $responseData['error'] ?? 0;
                $transaction->error_message = $responseData['errorMessage'] ?? '';
                $transaction->status = $responseData['status'] ?? 'error';
                $transaction->octo_payment_id = $responseData['octo_payment_UUID'] ?? '';
                $transaction->octo_pay_url = $responseData['octo_pay_url'] ?? '';
                if ($transaction->save()) {
                    return response()->json(['data' => [$transaction]]);
                } else {
                    return response()->json(['data' => ['error' => 'Не удалось создать транзакцию.']], 500);
                }
            } else {
                return response()->json($err, 500);
            }
        } else {
            return response()->json(['data' => ['error' => 'Не удалось создать транзакцию.']], 500);
        }
    }
}
