<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class InfoByIpService {
    public static function getInfoByIp($ipAddress)
    {
        $hash = 'geo_ip_{' . md5($ipAddress) . '}';
        $data = Cache::get($hash);
        $dataArray = @json_decode($data, true);

        if($dataArray !== null) {
            return $dataArray;
        }

        $ch = curl_init("http://ip-api.com/json/$ipAddress");
        curl_setopt_array($ch, [
            CURLOPT_POSTFIELDS => true,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $rsp = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $rspDecoded = @json_decode($rsp, true);

        if ($statusCode === 200 && $rspDecoded !== null) {
            Cache::add($hash, $rsp, 172800);
        }

        return json_decode($rsp, true);
    }
}


?>