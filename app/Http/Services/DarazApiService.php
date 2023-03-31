<?php

namespace App\Http\Services;

use Illuminate\Http\JsonResponse;
use Exception;
use Spatie\ArrayToXml\ArrayToXml;

class DarazApiService
{
    /**
     * @var DarazClientService
     */
    private $darazService;
    public $apiParams = [];

    /**
     * @param DarazClientService $darazService
     *
     */
    public function __construct(DarazClientService $darazService) {
        $this->darazService = $darazService;
    }


    /**
     * @param $authorizeCode
     * @return bool|JsonResponse|string|null
     */
    public function generateAccessToken($authorizeCode) {
        try {
            $apiName = "/auth/token/create";
            $method = 'POST';
            $apiParams = [
                'code' => $authorizeCode
            ];

            return $this->darazService->execute($apiParams, $apiName, $method);
        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }


    /**
     * @param $refreshToken
     * @return bool|JsonResponse|string|null
     */
    public function refreshAccessToken($refreshToken) {
        try {
            $apiName = "/auth/token/refresh";
            $method = 'POST';
            $this->apiParams = [
                'refresh_token' => $refreshToken
            ];

            return $this->darazService->execute($this->apiParams, $apiName, $method);
        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }


    /**
     * @param $accessToken
     * @return bool|JsonResponse|string|null
     */
    public function getSeller($accessToken) {
        try {
            $apiName = "/seller/get";
            $method = 'GET';

            return $this->darazService->execute($this->apiParams, $apiName, $method, $accessToken);
        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    /**
     * @return bool|JsonResponse|string|null
     */
    public function getCategoryTree() {
        try {
            $apiName = "/category/tree/get";
            $method = 'GET';

            return $this->darazService->execute($this->apiParams, $apiName, $method );
        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }

    }

    /**
     * @param $categoryId
     * @return bool|JsonResponse|string|null
     */
    public function getCategoryAttributes($categoryId) {
        try {
            $apiName = "/category/attributes/get";
            $method = 'GET';
            $this->apiParams = [
                'primary_category_id' => $categoryId
            ];

            return $this->darazService->execute($this->apiParams, $apiName, $method );
        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    private function buildProductPayload() {
        $array = [
            "Product" => [
                "PrimaryCategory" => "20000013",
                "SPUId" => [],
                "AssociatedSku" => [],
                "Images" => [
                    "Image" => [
                        0 => "https://my-live-02.slatic.net/p/765888ef9ec9e81106f451134c94048f.jpg",
                        1 => "https://my-live-02.slatic.net/p/9eca31edef9f05f7e42f0f19e4d412a3.jpg"
                    ]
                ],
                "Attributes" => [
                    "name" => "Handshake Module",
                    "short_description" => "This is a nice product",
//                    "brand_id"=>"23892",
                    "brand" => "AKG",
//      "model" => "asdf",
//      "kid_years" => "Kids (6-10yrs)",
//      "delivery_option_sof" => "Yes",
//      "comment" => []
                ],
                "Skus" => [
                    "Sku" => [
                        "SellerSku" => "handshake-api-create-test-2",
                        "color_family" => "Green",
                        "size" => "40",
                        "quantity" => "5",
                        "price" => "388",

                        "package_length" => "11",
                        "package_height" => "22",
                        "package_weight" => "33",
                        "package_width" => "44",
                        "package_content" => "this is what's in the box",
                        "Images" => [
                            "Image" => [
//            0 => "http://sg.s.alibaba.lzd.co/original/59046bec4d53e74f8ad38d19399205e6.jpg",
//            1 => "http://sg.s.alibaba.lzd.co/original/179715d3de39a1918b19eec3279dd482.jpg"
                            ]
                        ]
                    ]
                ]
            ]
        ];

        return ArrayToXml::convert(
            $array,
            'Request',
            true,
            "UTF-8"
        );


    }

    /**
     * @param $accessToken
     * @return bool|JsonResponse|string|null
     */
    public function createProduct($accessToken) {
        try {
            $apiName = "/product/create";
            $method = 'POST';
            $this->apiParams ['payload'] = $this->buildProductPayload();

            return $this->darazService->execute($this->apiParams, $apiName, $method, $accessToken);

        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }

    /**
     * @param string $startRow
     * @param string $pageSize
     * @return bool|JsonResponse|string
     */
    public function getBrandByPages(string $startRow='0', string $pageSize='20') {
        try {
            $apiName = "/category/brands/query";
            $method = 'GET';
            $this->apiParams = [
                'startRow' => $startRow,
                'pageSize' => $pageSize,
            ];

            return $this->darazService->execute($this->apiParams, $apiName, $method );
        } catch (Exception $e) {

            return response()->json($e->getMessage());
        }
    }
}
