<?php

namespace App\Http\Traits;
use GuzzleHttp\Client;
trait WablasTrait
{
    public static function sendText($data = [])
    {
        // $curl = curl_init();
        // error_log('testttt');
        // $token = env('SECURITY_TOKEN_WABLAS');
        // $payload = [
        //     "data" => $data
        // ];
        // curl_setopt(
        //     $curl,
        //     CURLOPT_HTTPHEADER,
        //     array(
        //         "Authorization: $token",
        //         "Content-Type: application/json"
        //     )
        // );
        
        // curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
        // curl_setopt($curl, CURLOPT_URL,  env('DOMAIN_SERVER_WABLAS') . "/api/v2/send-message");
        // curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

        // $result = curl_exec($curl);
        // curl_close($curl);


        $client = new Client();
        $token = env('SECURITY_TOKEN_WABLAS');
        $payload = [
            'data' => $data,
        ];

        try {
            $response = $client->post(env('DOMAIN_SERVER_WABLAS') . '/api/v2/send-message', [
                'headers' => [
                    'Authorization' => $token,
                    'Content-Type' => 'application/json',
                ],
                'json' => $payload,
            ]);

            $result = json_decode($response->getBody(), true);
            // Handle result as needed
            // You can access data using $result['key']
            // For example, $result['status']
            // ...

        } catch (\Exception $e) {
            // Handle error
            // You can access error details using $e->getMessage()
            // ...
            \Illuminate\Support\Facades\Log::error('Error sending message: ' . $e->getMessage());
        }
    }
}