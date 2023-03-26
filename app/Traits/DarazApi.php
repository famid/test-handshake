<?php


namespace App\Traits;

trait DarazApi{

    protected $base_url   = 'https://sandbox.ekshopdelivery.com/api/v1';
    protected $api_key    = "API-KEY: KEY_uzeV7RYOvUK7XzYD";
    protected $api_secret = "API-SECRET: SEC_4TokePsrSE8br3KWldmZdIOA";


    // Call Api for GET Method

    public function getApiData($url,$data)
    {

        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            $this->base_url.$url
        );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($data));

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            $this->api_key,
            $this->api_secret
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }





    // Call Api for POST Method

    public function postApiData($url,$data)
    {

        try {

            $ch = curl_init();
            curl_setopt(
                $ch,
                CURLOPT_URL,
                $this->base_url.$url
            );
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));


            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/json",
                $this->api_key,
                $this->api_secret
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            return $response;

        } catch (\Throwable $th) {

            dd($th);
        }

    }

}
