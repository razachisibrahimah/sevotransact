<?php

namespace RazakIbrahimah\Sevotransact;

use RazakIbrahimah\Sevotransact\Exceptions\CurlException;
use function Prinx\Dotenv\env;

class MomoCollection
{
    protected $transactionId;
    protected $baseUrl = 'https://sevotransact.com/api/v1/';

    public function pay($amount, $msisdn, $channel, $voucherCode = null)
    {
        if($channel === "VODAFONE" && is_null($voucherCode)) {
            throw new \Exception('sorry, voucher code is required when using VODAFONE channel.');
        } elseif ($channel !== 'VODAFONE' && $voucherCode) {
            throw new \Exception('Channel has to be VODAFONE when voucher code is passed.');
        }

        $credential = [
            "txtpay_api_id" => env("TXTPAY_API_ID"),
            "txtpay_api_key" => env("TXTPAY_API_KEY"),
        ];

        $token = $this->generateToken($credential);

        $payload = [
            "recipient" => $msisdn,
            "channel" => $channel,
            "primary-callback" => env("TXTPAY_CALL_BACK"),
            "amount" => $amount,
            "nickname" => env("TXTPAY_NICKNAME"),
            "description" => env("TXTPAY_DESCRIPTION"),
            "reference" => $this->getTransactionId()
        ];

        if ($voucherCode) {
            $payload["voucher-code"] = $voucherCode;
        }

        $response = $this->makePayment($token, $payload);

        return $response;
    }

    public function getTransactionId()
    {
        if (is_null($this->transactionId)) {
            $this->transactionId = str_pad(rand(1,999999999), 12, "0", STR_PAD_LEFT);
        }

        return $this->transactionId;
    }

    public function setTransactionId($id)
    {
        $this->transactionId = $id;

        return $this;
    }

    public function generateToken($credential)
    {
        $url = $this->baseUrl.env('TXTPAY_ACCOUNT')."/token";

        $response = $this->callApi($url, $credential);
        
        return $response['data']['token'];
    }

    public function makePayment($token, $request)
    {
        $url = $this->baseUrl.env('TXTPAY_ACCOUNT')."/payment-app/receive-money";

        $headers = ["authorization: Bearer $token"];

        return $this->callApi($url, $request, $headers);
    }

    public function callApi($url, $payload, $headers = [])
    {
        $curl = curl_init();

        $defaultHeaders = ['content-type: application/json'];

        $headers = array_merge($defaultHeaders, $headers);

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        $data = json_decode($response, true);

        if ($err || is_null($data)) {
            throw new CurlException($err ?: $response);
        }

        return $data;
    }
}