<?php

namespace App\Http\Controllers;

use App\Http\Services\DarazApiService;
use App\Models\DarazIntegration;
use App\Models\User;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

            if($this->isDarazSellerAccountExist(activeShop()->id, $getAccessToken->account))
                throw new Exception('This seller account is already used by another user');

            $storeAccessToken = $this->darazIntegrationModel->where('id', $storeDarazCode->id)->update([
                'access_token' => $getAccessToken->access_token,
                'refresh_token' => $getAccessToken->refresh_token,
                'daraz_account_email' => $getAccessToken->account,
            ]);

            if (!$storeAccessToken) throw new Exception('Access token does not found from daraz');

            $updateStatus = $this->userModel->where('id', Auth::user()->id)->update([
                'daraz_account_status' => 'ACTIVE'
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
        return activeShop()->where([
            ['shop_email', $email],
            ['user_id', '<>', $userId]
        ])->exists();
    }
}
