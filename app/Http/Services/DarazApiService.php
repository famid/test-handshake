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

    private function buildProductPayload($payload=null) {
        $array = [
            "Product" => [
                "PrimaryCategory" => "1740",
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
                        "saleProp" => [
                            "color_family" => ["Green", "Gold"],
                        ],
                        "SellerSku" => "local-sku",
                        "color_family" => "Green",
//                        "size" => "40",
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
        $Image = [
//            0 => "https://my-live-02.slatic.net/p/765888ef9ec9e81106f451134c94048f.jpg",
//            1 => "https://my-live-02.slatic.net/p/9eca31edef9f05f7e42f0f19e4d412a3.jpg"
            0 => "https://static-01.daraz.com.bd/p/32ac6673eef212b0a506dc325a1d0198.jpg",
//            1 => "https://www.daraz.com.bd/products/echolac-4-7005-18-inc-i203620471-s1151673897.html?spm=a2a0e.searchlistcategory.list.1.62cd13dbTdsvBe&search=1"
        ];
//        $categoryId = $payload['PrimaryCategory'];
//        unset($payload['PrimaryCategory']);
//        $payload['Product']['PrimaryCategory'] = $categoryId;
//        $payload['Product']["Images"] = ['Image' => $Image];


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
    public function createProduct($accessToken, $payload=null) {
        try {
            $apiName = "/product/create";
            $method = 'POST';
            $this->apiParams ['payload'] = $this->buildProductPayload($payload);
//            dd($this->apiParams ['payload']);
//            $this->apiParams ['payload'] = $payload;

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
