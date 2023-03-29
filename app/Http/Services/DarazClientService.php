<?php

namespace App\Http\Services;

use Exception;

class DarazClientService
{
    /**
     * This is an app key of the system
     *
     * @var string
     */
    private $appKey;

    /**
     * This is a secret key of the system
     *
     * @var string
     */
    private $secretKey;

    /**
     * (base url of daraz)
     *
     * @var string
     */
    private $gatewayUrl;


    /**
     * This is the signMethod name for generate signature
     *
     * @var string
     */
    protected $signMethod = "sha256";


    /**
     * This is member may not be needed
     *
     * @var string
     */
    protected $sdkVersion = "lazop-sdk-php-20180422";

    /**
     *
     */
    public function __construct() {
        $this->appKey = config('services.daraz.app_key');
        $this->secretKey = config('services.daraz.secret_key');
        $this->gatewayUrl = config('services.daraz.gate_way_url');
    }


    /**
     * @param $request
     * @param string $apiName
     * @param string $httpMethod
     * @param $accessToken
     * @return bool|string
     */
    public function execute($request, string $apiName, string $httpMethod="POST", $accessToken=null) {
        try {
            $apiParams = $request;
            $sysParams = $this->buildSystemParams($apiName, $apiParams, $accessToken);
            $requestUrl = $this->buildRequestUrl($apiName, $sysParams);

            if($httpMethod == 'POST') {
                return $this->postApiData($requestUrl, $apiParams);
            }

            return $this->getApiData($requestUrl, $apiParams);
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    /**
     * @param $apiName
     * @param $apiParams
     * @param $accessToken
     * @return array
     */
    private function buildSystemParams($apiName, $apiParams, $accessToken=null): array {
        $sysParams = [
            "app_key" => $this->appKey,
            "sign_method" => $this->signMethod,
            'timestamp' => $this->generateValidTimestamp(),
        ];

        if (isset($accessToken)) {
            $sysParams["access_token"] = $accessToken;
        }

        $sysParams["sign"] = $this->generateSignature($apiName, array_merge($apiParams, $sysParams));

        return $sysParams;
    }

    /**
     * @return int
     */
    private function generateValidTimestamp(): int {
        // Get current UTC timestamp
        $current_utc_timestamp = round(microtime(true) * 1000);

        // Generate a random timestamp within the 7200 seconds range
        $min_timestamp = $current_utc_timestamp - 7200 * 1000;
        $max_timestamp = $current_utc_timestamp + 7200 * 1000;
        $valid_timestamp = mt_rand($min_timestamp, $max_timestamp);

        // Check if the generated timestamp is within the 7200 seconds range
        $utc_time = gmdate('Y-m-d H:i:s', $valid_timestamp / 1000);
        $current_utc_time = gmdate('Y-m-d H:i:s');
        $time_difference = strtotime($current_utc_time) - strtotime($utc_time);

        return (abs($time_difference) <= 7200)  ? $valid_timestamp  : $this->generateValidTimestamp();
    }

    /**
     * @param string $apiName
     * @param array $apiAllParams
     * @return string
     */
    protected function generateSignature(string $apiName, array $apiAllParams): string {
        try {
            ksort($apiAllParams);

            $stringToBeSigned = $apiName;

            foreach ($apiAllParams as $key => $value) {
                $stringToBeSigned .= "$key$value";
            }
            unset($key, $value);

            return strtoupper($this->hmac_sha256($stringToBeSigned, $this->secretKey));
        } catch (Exception $e) {

            return $e->getMessage();
        }
    }

    /**
     * @param $data
     * @param $key
     * @return string
     */
    function hmac_sha256($data, $key): string {
        return hash_hmac('sha256', $data, $key);
    }

    /**
     * @param $apiName
     * @param $sysParams
     * @return string
     */
    private function buildRequestUrl($apiName, $sysParams): string {
        $requestUrl = $this->gatewayUrl;

        if ($this->endWith($requestUrl, "/")) {
            $requestUrl = substr($requestUrl, 0, -1);
        }

        $requestUrl .= $apiName;
        $requestUrl .= '?' . http_build_query($sysParams);

        return $requestUrl;
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    function endWith($haystack, $needle): bool {
        $length = strlen($needle);
        return !($length == 0) && substr($haystack, - $length) === $needle;
    }


    /**
     * @param $apiUrl
     * @param $postFields
     * @param $fileFields
     * @param $headerFields
     * @return bool|string
     * @throws Exception
     */
    public function postApiData($apiUrl, $postFields = null, $fileFields = null, $headerFields = null) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_FAILONERROR, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if($headerFields) {
            $headers = array();
            foreach ($headerFields as $key => $value)
            {
                $headers[] = "$key: $value";
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            unset($headers);
        }

        curl_setopt ( $ch, CURLOPT_USERAGENT, $this->sdkVersion );

        //https ignore ssl check ?
        if(strlen($apiUrl) > 5 && strtolower(substr($apiUrl,0,5)) == "https" ) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $delimiter = '-------------' . uniqid();
        $data = '';

        if($postFields != null) {
            foreach ($postFields as $name => $content)
            {
                $data .= "--" . $delimiter . "\r\n";
                $data .= 'Content-Disposition: form-data; name="' . $name . '"';
                $data .= "\r\n\r\n" . $content . "\r\n";
            }
            unset($name,$content);
        }

        if($fileFields != null)
        {
            foreach ($fileFields as $name => $file)
            {
                $data .= "--" . $delimiter . "\r\n";
                $data .= 'Content-Disposition: form-data; name="' . $name . '"; filename="' . $file['name'] . "\" \r\n";
                $data .= 'Content-Type: ' . $file['type'] . "\r\n\r\n";
                $data .= $file['content'] . "\r\n";
            }
            unset($name,$file);
        }

        $data .= "--" . $delimiter . "--";

        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER ,
            array(
                'Content-Type: multipart/form-data; boundary=' . $delimiter,
                'Content-Length: ' . strlen($data)
            )
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

        $response = curl_exec($ch);
        unset($data);

        $errno = curl_errno($ch);
        if ($errno)
        {
            curl_close($ch);
            throw new Exception($errno,0);
        }
        else
        {
            $httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if (200 !== $httpStatusCode)
            {
                throw new Exception($response,$httpStatusCode);
            }
        }

        return $response;
    }

    /**
     * @param $apiUrl
     * @param $data
     * @return bool|string
     */
    public function getApiData($apiUrl, $data) {
        $apiUrl .= '&' . http_build_query($data);


        $ch = curl_init();
        curl_setopt(
            $ch,
            CURLOPT_URL,
            $apiUrl
        );

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'GET' );

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json"
        ));

        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }
}
