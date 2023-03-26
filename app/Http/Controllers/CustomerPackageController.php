<?php

namespace App\Http\Controllers;

use App\Utility\PayfastUtility;
use Illuminate\Http\Request;
use App\Models\CustomerPackage;
use App\Models\CustomerPackageTranslation;
use App\Models\CustomerPackagePayment;
use Auth;
use Session;
use App\Models\User;
use App\Http\Controllers\PublicSslCommerzPaymentController;
use App\Http\Controllers\InstamojoController;
use App\Http\Controllers\RazorpayController;
use App\Http\Controllers\VoguePayController;
use App\Models\Package;
use App\Models\PackageUser;
use App\Traits\TransactionTrait;
use App\Utility\PayhereUtility;
use Carbon\Carbon;


class CustomerPackageController extends Controller
{
    use TransactionTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customer_packages = CustomerPackage::all();
        return view('backend.customer.customer_packages.index', compact('customer_packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.customer.customer_packages.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $customer_package = new CustomerPackage;
        $customer_package->name = $request->name;
        $customer_package->amount = $request->amount;
        $customer_package->product_upload = $request->product_upload;
        $customer_package->logo = $request->logo;

        $customer_package->save();

        $customer_package_translation = CustomerPackageTranslation::firstOrNew(['lang' => env('DEFAULT_LANGUAGE'), 'customer_package_id' => $customer_package->id]);
        $customer_package_translation->name = $request->name;
        $customer_package_translation->save();


        flash(translate('Package has been inserted successfully'))->success();
        return redirect()->route('customer_packages.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $lang = $request->lang;
        $customer_package = CustomerPackage::findOrFail($id);
        return view('backend.customer.customer_packages.edit', compact('customer_package', 'lang'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $customer_package = CustomerPackage::findOrFail($id);
        if ($request->lang == env("DEFAULT_LANGUAGE")) {
            $customer_package->name = $request->name;
        }
        $customer_package->amount = $request->amount;
        $customer_package->product_upload = $request->product_upload;
        $customer_package->logo = $request->logo;

        $customer_package->save();

        $customer_package_translation = CustomerPackageTranslation::firstOrNew(['lang' => $request->lang, 'customer_package_id' => $customer_package->id]);
        $customer_package_translation->name = $request->name;
        $customer_package_translation->save();

        flash(translate('Package has been updated successfully'))->success();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer_package = CustomerPackage::findOrFail($id);
        foreach ($customer_package->customer_package_translations as $key => $customer_package_translation) {
            $customer_package_translation->delete();
        }
        CustomerPackage::destroy($id);

        flash(translate('Package has been deleted successfully'))->success();
        return redirect()->route('customer_packages.index');
    }

    public function purchase_package(Request $request)
    {

        $data['customer_package_id'] = $request->customer_package_id;
        $data['payment_method']      = $request->payment_option;
        $customer_package = Package::findOrFail($request->customer_package_id);
        $current_package  = checkPackage();
        $discount         = 0;
        if ($current_package != null && $current_package->package->package_price > 0) {
            $remain_days = countDays(date("Y-m-d H:i:s"),$current_package->subscription_end);
            $discount    = floor(($current_package->package->package_price / $current_package->package->package_duration) * $remain_days);
        }

        $data['package_price'] = $customer_package->package_price - $discount;




        $request->session()->put('payment_type', 'customer_package_payment');
        $request->session()->put('payment_data', $data);

        // $customer_package = CustomerPackage::findOrFail(Session::get('payment_data')['customer_package_id']);


        if ($request->total_amount == 0) {
            // $user = User::findOrFail(Auth::user()->id);

            return $this->purchase_payment_done(Session::get('payment_data'), null);

            // if ($user->customer_package_id != $customer_package->id) {
            //     return $this->purchase_payment_done(Session::get('payment_data'), null);
            // } else {
            //     flash(translate('You can not purchase this package anymore.'))->warning();
            //     return back();
            // }
        }

        if ($request->payment_option == 'paypal') {
            $paypal = new PaypalController;
            return $paypal->getCheckout();
        } elseif ($request->payment_option == 'stripe') {
            $stripe = new StripePaymentController;
            return $stripe->stripe();
        } elseif ($request->payment_option == 'sslcommerz') {
            $sslcommerz = new PublicSslCommerzPaymentController;
            return $sslcommerz->index($request);
        } elseif ($request->payment_option == 'instamojo') {
            $instamojo = new InstamojoController;
            return $instamojo->pay($request);
        } elseif ($request->payment_option == 'razorpay') {
            $razorpay = new RazorpayController;
            return $razorpay->payWithRazorpay($request);
        } elseif ($request->payment_option == 'paystack') {
            $paystack = new PaystackController;
            return $paystack->redirectToGateway($request);
        } elseif ($request->payment_option == 'voguepay') {
            $voguePay = new VoguePayController;
            return $voguePay->customer_showForm();
        } elseif ($request->payment_option == 'payhere') {
            $order_id = rand(100000, 999999);
            $user_id = Auth::user()->id;
            $package_id = $request->customer_package_id;
            $amount = $customer_package->amount;
            $first_name = Auth::user()->name;
            $last_name = 'X';
            $phone = '123456789';
            $email = Auth::user()->email;
            $address = 'dummy address';
            $city = 'Colombo';

            return PayhereUtility::create_customer_package_form($user_id, $package_id, $order_id, $amount, $first_name, $last_name, $phone, $email, $address, $city);
        } elseif ($request->payment_option == 'payfast') {
            $user_id = Auth::user()->id;
            $package_id = $request->customer_package_id;
            $amount = $customer_package->amount;

            return PayfastUtility::create_customer_package_form($user_id, $package_id, $amount);
        } elseif ($request->payment_option == 'ngenius') {
            $ngenius = new NgeniusController();
            return $ngenius->pay();
        } else if ($request->payment_option == 'iyzico') {
            $iyzico = new IyzicoController();
            return $iyzico->pay();
        } else if ($request->payment_option == 'nagad') {
            $nagad = new NagadController();
            return $nagad->getSession();
        } else if ($request->payment_option == 'bkash') {
            $bkash = new BkashController();
            return $bkash->pay();
        } else if ($request->payment_option == 'mpesa') {
            $mpesa = new MpesaController();
            return $mpesa->pay();
        } else if ($request->payment_option == 'flutterwave') {
            $flutterwave = new FlutterwaveController();
            return $flutterwave->pay();
        }
    }

    public function purchase_payment_done($payment_data, $payment)
    {
        $payment = json_decode($payment);
        $user    = User::findOrFail(Auth::user()->id);
        $package = Package::find($payment_data['customer_package_id']);
        $package_user = PackageUser::where('user_id',Auth::user()->id)->where('user_type',loginType())->first();

        if ($payment != null) {
            $transaction_additional = [
                'tran_id'      => $payment->{'tran_id'},
                'bank_tran_id' => $payment->{'bank_tran_id'},
                'tran_date'    => $payment->{'tran_date'},
                'card_issuer'  => $payment->{'card_issuer'},
            ];
        }else{
            $transaction_additional = null;
        }


        if ($package_user == null) {

            $package_entries = PackageUser::create([

                'package_id'         =>  $package->id,
                'user_id'            => $user->id,
                'user_type'          => loginType(),
                'remain_product'     => $package->product_limit,
                'remain_warehouse'   => $package->warehouse_limit,
                'remain_daraz_sync'  => $package->daraz_sync_limit,
                'subscription_start' => Carbon::now(),
                'subscription_end'   => $package->package_duration > 0 ? Carbon::now()->addDays($package->package_duration) : null,
                'status'             => 1,

            ]);

            $transaction_data = [
                'user_id'            => Auth::user()->id,
                'user_type'          => loginType(),
                'transaction_id'     => 'PCK-'.rand(10000,50000),
                'transaction_amount' => $payment != null ? $payment->{'amount'} : 0,
                'refrence_id'        => $package_entries->id,
                'type'               => 'customer_package_payment',
                'note'               => 'Purchase new package',
                'gateway'            => $payment_data['payment_method'],
                'additional_content' => $payment != null ? json_encode($transaction_additional) : null,
                'payment_type'       => $payment->{'card_type'} ?? null
            ];

            $this->MakeTransaction($transaction_data);
        }else{


            $package_user->update([

                'package_id'         => $package->id,
                'user_id'            => $user->id,
                'user_type'          => loginType(),
                'remain_product'     => $this->calculateRemain($package_user->remain_product,$package->product_limit,$package_user->Package->product_limit),
                'remain_warehouse'   => $this->calculateRemain($package_user->remain_warehouse,$package->warehouse_limit,$package_user->Package->warehouse_limit),
                'remain_daraz_sync'  => $this->calculateRemain($package_user->remain_daraz_sync,$package->daraz_sync_limit,$package_user->Package->daraz_sync_limit),
                'subscription_start' => Carbon::now(),
                'subscription_end'   => $package->package_duration > 0 ? Carbon::now()->addDays($package->package_duration) : null,
                'status'             => 1
            ]);

            $transaction_data = [
                'user_id'            => Auth::user()->id,
                'user_type'          => loginType(),
                'transaction_id'     => 'PCK-'.rand(10000,50000),
                'transaction_amount' => $payment != null ? $payment->{'amount'} : 0,
                'refrence_id'        => $package_user->id,
                'type'               => 'customer_package_payment',
                'note'               => 'Upgrade new package',
                'gateway'            => $payment_data['payment_method'],
                'additional_content' => $payment != null ? json_encode($transaction_additional) : null,
                'payment_type'       => $payment->{'card_type'} ?? null
            ];

            $this->MakeTransaction($transaction_data);
        }
        // $user->customer_package_id = $payment_data['customer_package_id'];
        // $customer_package = CustomerPackage::findOrFail($payment_data['customer_package_id']);
        // $customer_package = Package::findOrFail($payment_data['customer_package_id']);
        // $user->remaining_uploads += $customer_package->product_limit;
        // $user->save();



        flash(translate('Package purchasing successful'))->success();
        return redirect()->route('dashboard');
    }

    public function purchase_package_offline(Request $request)
    {
        $customer_package = new CustomerPackagePayment;
        $customer_package->user_id = Auth::user()->id;
        $customer_package->customer_package_id = $request->package_id;
        $customer_package->payment_method = $request->payment_option;
        $customer_package->payment_details = $request->trx_id;
        $customer_package->approval = 0;
        $customer_package->offline_payment = 1;
        $customer_package->reciept = ($request->photo == null) ? '' : $request->photo;
        $customer_package->save();
        flash(translate('Offline payment has been done. Please wait for response.'))->success();
        return redirect()->route('customer_products.index');
    }


    public function getPackageInfo($id)
    {
        try {

            $package         = Package::find($id);
            $current_package = checkPackage();
            $view            = view('frontend.inc.package_purchase_modal',compact('package','current_package'))->render();

            return response()->json([
                'result'  => true,
                'data'    => $view
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'result'  => false,
                'message' => $th->getMessage()
            ]);
        }
    }


    private function calculateRemain($current_remain,$new_pkg_limit,$current_pkg_limit)
    {
        $remaining = $current_remain;
        $remaining += ($new_pkg_limit - $current_pkg_limit);

        return $remaining;
    }


    public function renewPackage()
    {

        try {

            $current_package = checkPackage();
            $view            = view('frontend.inc.package_renew_modal',compact('current_package'))->render();

            return response()->json([
                'result'  => true,
                'data'    => $view
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'result'  => false,
                'message' => $th->getMessage()
            ]);
        }

    }


    public function renewPackageUpdate(Request $request)
    {
        try {

            $request->session()->put('payment_type', 'customer_package_renew_payment');

            if ($request->payment_option == 'sslcommerz') {
                $sslcommerz = new PublicSslCommerzPaymentController;
                return $sslcommerz->index($request);
            }


        } catch (\Throwable $th) {

            flash(translate('Something went wrong! Please try again.'))->error();
            return redirect()->back();
        }
    }


    public function renew_package_payment_done($payment_data, $payment)
    {
        try {

            $package = PackageUser::find($payment_data['customer_package_id']);
            $payment = json_decode($payment);
            $package->update([
                'subscription_end' => date('Y-m-d', strtotime($package->subscription_end. " +".$package->Package->package_duration." days"))
            ]);

            if ($payment != null) {
                $transaction_additional = [
                    'tran_id'      => $payment->{'tran_id'},
                    'bank_tran_id' => $payment->{'bank_tran_id'},
                    'tran_date'    => $payment->{'tran_date'},
                    'card_issuer'  => $payment->{'card_issuer'},
                ];
            }else{
                $transaction_additional = null;
            }

            $transaction_data = [
                'user_id'            => Auth::user()->id,
                'user_type'          => loginType(),
                'transaction_id'     => 'PCK-'.rand(10000,50000),
                'transaction_amount' => $payment != null ? $payment->{'amount'} : 0,
                'refrence_id'        => $package->id,
                'type'               => 'customer_package_renew_payment',
                'note'               => 'Renew Package',
                'gateway'            => $payment_data['payment_method'],
                'additional_content' => $payment != null ? json_encode($transaction_additional) : null,
                'payment_type'       => $payment->{'card_type'} ?? null
            ];

            $this->MakeTransaction($transaction_data);

            flash(translate('Package renew succesfully.'))->success();
            return redirect()->route('dashboard');

        } catch (\Throwable $th) {
            dd($th);
            flash(translate('Something went wrong! Please try again.'))->error();
            return redirect()->route('dashboard');
        }
    }
}
