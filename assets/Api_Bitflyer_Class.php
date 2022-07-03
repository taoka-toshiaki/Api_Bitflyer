<?php
date_default_timezone_set('Asia/Tokyo');
class Api_Bitflyer_Class
{
    var $basic_url = "https://api.bitflyer.com";
    public function __construct()
    {
        require_once __DIR__."/../common/init.php";
    }
    /**
     * @param timestamp $timestamp
     * @param string $path
     * @param string $method
     * @param string $body
     */  
    public function api_set($timestamp = "", $path = "/v1/me/getbalance", $method = "GET", $body = "")
    {

        $url = $this->basic_url . $path;
        $data = strtolower($method) === "get" ? $timestamp . $method . $path : $timestamp . $method . $path . (function ($body) {
            if (!is_array($body)) {
                $body = [];
            }
            return json_encode($body);
        })($body);
        $access_singn = hash_hmac("sha256", $data, APISECRET);
        $headers = [
            'ACCESS-KEY: ' . APIKEY,
            'ACCESS-TIMESTAMP: ' . $timestamp,
            'ACCESS-SIGN: ' . $access_singn,
            'Content-Type: application/json',
        ];
        return new class($url, $method, $headers, $body)
        {
            var $url = "";
            var $method = "";
            var $headers = "";
            var $body = "";
            /**
             * @param string $url
             * @param string $method
             * @param string $headers
             * @param string $body
             */              
            public function __construct($url, $method, $headers, $body)
            {
                $this->url = $url;
                $this->method = $method;
                $this->headers = $headers;
                $this->body = $body;
            }
            public function api_run()
            {
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_URL, $this->url);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $this->method);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
                strtolower($this->method) == "post" ? curl_setopt($curl, CURLOPT_POSTFIELDS, $this->body) : "";
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $response             = curl_exec($curl);
                // $response_info        = curl_getinfo($curl);
                // $response_code        = $response_info['http_code'];
                // $response_header_size = $response_info['header_size'];
                curl_close($curl);
                print date("Y/m/d H:i:s").PHP_EOL;
                print " --- Response data start ---".PHP_EOL;
                var_dump(json_decode($response));
                print " --- Response data end ---".PHP_EOL;

                // var_dump($response_code);
                // var_dump($response_header_size);
            }
        };
    }
}

$Api_Bitflyer_Class = new Api_Bitflyer_Class();
$Api_Bitflyer_Class->api_set(time(),"/v1/me/getbalance","GET","")->api_run();
