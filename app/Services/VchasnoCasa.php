<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;

class VchasnoCasa
{
    protected $httpClient;

   public function __construct()
   {
    $this->httpClient = new Client();

    }

   public function sendPostRequestWithAuthorization($user_info, $rows, $pays, $sum)
    {
        //dd([$rows, $pays, $sum]);
        try {
            $headers = [
                'Authorization' => 'm5wItBxX8jghcVwTEkGdyCl1T30AoOjMu61Hb3O4jKCz_48VqvFYR18ss_1lm8F3',
                'Content-Type' => 'application/json',
            ];
            $response = $this->httpClient->post('https://kasa.vchasno.ua/api/v3/fiscal/execute', [
                'json' => $this->convertArrayToJson($user_info, $rows, $pays, $sum),
                'headers' => $headers,
            ]);

            // Handle the response here if needed
            $res = $response->getBody()->getContents();
            return json_decode($res);
        } catch (\Exception $e) {
            // Handle any exceptions or errors
            Log::error($response->getBody()->getContents());
            return $e->getMessage();
        }
    }

    function convertArrayToJson($user_info, $rows, $pays, $sum) {
        $data = [
            "source" => "OwnCMS",
            "userinfo" => [
                "phone" => $user_info,
                "email" => ""
            ],
            "fiscal"=>[
                'task'=>1,
                'receipt'=>[
                    'sum'=>$sum,
                    'comment_up'=>'',
                    'comment_down'=>'ДЯКУЄМО за покупку',
                    'rows'=>$rows->toArray(),
                    'pays'=>$pays->toArray()
                ],
            ]
        ];
        \Log::error($data);


        // Use JSON_UNESCAPED_UNICODE to keep Unicode characters intact
        //$json = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $data;
    }
}
