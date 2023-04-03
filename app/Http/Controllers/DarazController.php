<?php

namespace App\Http\Controllers;

use App\Http\Services\DarazApiService;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DarazIntegration;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Str;
use Throwable;

class DarazController extends Controller
{

    /**
     * @var DarazApiService
     */
    private $apiService;
    /**
     * @var DarazIntegration
     */
    private $darazIntegration;
    /**
     * @var DarazIntegration
     */
    private $darazIntegrationModel;
    /**
     * @var UserModel
     */
    private $userModel;

    public function __construct(
        DarazApiService $apiService,
        DarazIntegration $darazIntegrationModel,
        User $userModel
    ) {
        $this->apiService = $apiService;
        $this->darazIntegrationModel = $darazIntegrationModel;
        $this->userModel = $userModel;
    }

    public function updateSettings(Request $request)
    {
        try {

            $setting = $this->darazIntegrationModel->where('user_id', Auth::user()->id)->first();

            if ($setting) {

                $setting->update([
                    'app_key'    => $request->app_key,
                    'secret_key' => $request->secret_key,
                ]);
            }else{

                $this->darazIntegrationModel->wheree([
                    'user_id'    => Auth::user()->id,
                    'app_key'    => $request->app_key,
                    'secret_key' => $request->secret_key,
                ]);
            }

            flash(translate('Daraz setting update succesfully'))->success();
            return redirect()->back();

        } catch (Throwable $th) {

            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function darazCallback(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();


            if($this->isDarazAccountConnected(activeShop())) throw new Exception('Your account is already connected');

            if(!isset($request->state) || activeShop()->slug != $request->state) {
                throw new Exception('Invalid User slug');
            }

            $storeDarazCode = $this->darazIntegrationModel->updateOrCreate(
                [
                    'user_id' => Auth::user()->id,
                    'user_code' => $request->code,
                    'shop_id'   => activeShop()->id
                ]
            );

            if (!$storeDarazCode) throw new Exception('Daraz code does not found');

            $getAccessToken = json_decode($this->apiService->generateAccessToken( $request->code));

            if(!isset($getAccessToken->access_token) || !isset($getAccessToken->refresh_token)) {
                throw new Exception('Access token does not found from daraz');
            }

            if(!$this->isDarazSellerAccountExist(activeShop()->id, $getAccessToken->account))
                throw new Exception('This seller account is already used by another user');

            $storeAccessToken = $this->darazIntegrationModel->where('id', $storeDarazCode->id)->update([
                'access_token' => $getAccessToken->access_token,
                'refresh_token' => $getAccessToken->refresh_token,
                'daraz_account_email' => $getAccessToken->account,
            ]);

            if (!$storeAccessToken) throw new Exception('Access token does not found from daraz');

            $updateStatus = activeShop()->update([
                'status' => 1
            ]);

            if (!$updateStatus) throw new Exception('User status does not updated');

            DB::commit();
            flash(translate('Daraz setting update successfully'))->success();
            return redirect()->route('dashboard');

        } catch (Exception  $e) {

            DB::rollBack();
            flash(translate($e->getMessage()))->error();
            return redirect()->route('dashboard');
        }
    }

    /**
     * @throws Exception
     */
    private function isDarazAccountConnected($userObject): bool {
        return $userObject->status == 1;
    }

    /**
     * @return RedirectResponse
     */
    public function disconnectAccount(): RedirectResponse
    {
        try {

            activeShop()->update([
                'status' => 0
            ]);

            if (!activeShop()) throw new Exception('Something went wrong');

            flash(translate('Daraz account is successfully disconnected'))->success();
            return redirect()->route('dashboard');

        } catch (Exception $e) {
            flash(translate($e->getMessage()))->error();
            return redirect()->route('dashboard');

        }
    }


    /**
     * @param $userId
     * @param $email
     * @return mixed
     */
    private function isDarazSellerAccountExist($userId, $email) {
        if(activeShop()->shop_email == $email){

            return true;
        }
        return false;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAttributes(Request $request): JsonResponse
    {
        try {
            $data = $this->apiService->getCategoryAttributes($request->category_id);

//            $renderViewResponse = view(
//                'backend.product.products.product_daraz_attributes',
//                ['attributes' => json_decode($data, true)]
//            )->render();
            return response()->json([
                'result' => true,
                'data' => $data
            ]);
        } catch (Exception $e) {

            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param Category $categoryModel
     * @param string $filePath
     * @return JsonResponse
     */
    public function insertCategory(Category $categoryModel, string $filePath='public/category.json'): JsonResponse {
        try {
            $jsonString = file_get_contents($filePath);
            $categoriesApiResponse = json_decode($jsonString, true);
            $categories = [];

            $this->buildCategoryData($categoriesApiResponse['data'], $categories, 0, 0);

            $categoryModel->insert($categories);

            return response()->json(['success' => true, 'message' => "Data is inserted to database successfully"]);

        } catch (Exception $e) {

            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @param $node
     * @param $categories
     * @param $parentId
     * @param $level
     * @return void
     */
    public function buildCategoryData($node, &$categories, $parentId, $level) {
        if (!empty($node)) {
            for($i = 0; $i < count($node); $i++) {
                $currentNode = $node[$i];
                $dataToInsert = [
                    'id' => $currentNode['category_id'],
                    'name' => $currentNode['name'],
                    'parent_id' => $parentId,
                    'level' => $level,
                    'slug' => Str::slug($currentNode['name']),
                    'featured' => 1,
                    'allow_create_product' => $currentNode['leaf']
                ];

                $categories[] = $dataToInsert;
                unset($dataToInsert);

                if(isset($currentNode['children'])) {
                    $this->buildCategoryData(
                        $currentNode['children'],
                        $categories,
                        $currentNode['category_id'],
                        $level + 1
                    );
                }
            }
        }
    }

    /**
     * This function is used for fetch all brands list from daraz
     * and store to brands table in database
     * @return void
     */
    public function fetchAllBrandsAndStore() {
        $brands = [];
        $pageSize = 200;
        $startRow = 0;
        do {
            // Make the API request and decode the JSON response
            $response = json_decode($this->apiService->getBrandByPages($startRow, $pageSize), true);

            // Extract the brands from the current API response
            $module = $response['data']['module'];

            foreach ($module as $brand) {
                $brands[] = [
                    'id' => $brand['brand_id'],
                    'name' => $brand['name'],
                    'slug' => Str::slug($brand['name']),
                    'top' => 0,
                ];
            }

            // Increase the startRow parameter for the next API request
            $startRow = count($brands);

            // Sleep for a short period of time to avoid overloading the server
            usleep(500000);

            // $response['data']['total_record' == total number of data
        } while (count($brands) < $response['data']['total_record']);


        // Store the brands in a JSON file
//        file_put_contents(public_path('brand_list.json'), json_encode($brands));

        try {
            $batchSize = 1000;
            $chunks = array_chunk($brands, $batchSize);

            foreach ($chunks as $chunk) {
                Brand::insert($chunk);
            }

        } catch (Exception $e) {

            dd($e->getMessage());
        }
    }

    public function createProduct(Request $request) {

        $accessToken = "500009016320lwdjscVOjWhCvAKuzFiiRj3GuWkFXjxq1bfb4453hROWtgIx05";
        dd($this->apiService->createProduct($accessToken, $request->payload));
    }

}
