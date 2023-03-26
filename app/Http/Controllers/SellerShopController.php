<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\DarazShop;
use App\Traits\FileSaver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use App\Mail\SecondEmailVerifyMailManager;

class SellerShopController extends Controller
{
    use FileSaver;
    public function index()
    {
        $shops = DarazShop::where('user_id',Auth::user()->id)->get();
        return view('frontend.shop.shop_interface',compact('shops'));
    }



    public function store(Request $request)
    {
        try {

            $check = DarazShop::where('shop_email',$request->email)->first();

            if ($check == null) {

                $shop = DarazShop::create([
                    'user_id' => Auth::user()->id,
                    'shop_name' => $request->name,
                    'shop_email' => $request->email,
                    'otp_token'  => rand(1000,2000)

                ]);

                $this->GenerateSlug('slug',$shop,$request->name);

                $array['view'] = 'emails.verification';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = translate('Shop Verification');
                $array['content'] = translate('Verification Code is ').$shop->otp_token;

                Mail::to($shop->shop_email)->queue(new SecondEmailVerifyMailManager($array));

                return response()->json([
                    'result' => true,
                    'email'  => $shop->shop_email,
                    'message' => 'An Email sent to your email! Please verify your email.'
                ]);

            }else{

                return response()->json([
                    'result' => false,
                    'message' => 'This email is already used!.'
                ]);
            }



        } catch (\Throwable $th) {

            return response()->json([
                'result' => false,
                'message' => $th->getMessage()
            ]);
        }
    }


    public function verifyShop(Request $request)
    {
        try {

            $shop = DarazShop::where('shop_email',$request->verified_email)->first();

            if ($shop != null) {

                if ($shop->otp_token == $request->verify_otp) {

                    $shop->update([
                        'verified_at' => Carbon::now()
                    ]);

                    return response()->json([
                        'result' => true,
                        'message' => 'Email verified succesfully.',
                        'slug'    => $shop->slug
                    ]);

                }else{

                    return response()->json([
                        'result' => false,
                        'message' => 'OTP doesn\'t match!'
                    ]);
                }

            }else{

                return response()->json([
                    'result' => false,
                    'message' => 'Shop email invalid!'
                ]);
            }

        } catch (\Throwable $th) {

            return response()->json([
                'result' => false,
                'message' => 'Something went wrong! Please try again.'
            ]);
        }
    }

    public function reVerifyShop(Request $request)
    {
        try {

            $shop = DarazShop::where('shop_email',$request->email)->first();

            if ($shop != null) {

                $shop->update([
                    'otp_token' => rand(1000,2000)
                ]);

                $array['view'] = 'emails.verification';
                $array['from'] = env('MAIL_FROM_ADDRESS');
                $array['subject'] = translate('Shop Verification');
                $array['content'] = translate('Verification Code is ').$shop->otp_token;

                Mail::to($shop->shop_email)->queue(new SecondEmailVerifyMailManager($array));

                return response()->json([
                    'result' => true,
                    'email'  => $shop->shop_email,
                    'message' => 'An Email sent to your email! Please verify your email.'
                ]);
            }else{

                return response()->json([
                    'result' => false,
                    'message' => 'Shop email invalid!'
                ]);
            }


        } catch (\Throwable $th) {

            return response()->json([

                'result' => false,
                'message' => 'Something went wrong! Please try again.'
            ]);

        }
    }


    public function activeShop($slug)
    {
        try {

            $shop = DarazShop::where('slug',$slug)->first();
            Session::put('daraz_active_shop',$shop->id);

            flash(translate('Switched to shop '.$shop->shop_name))->success();
            return redirect()->route('dashboard');

        } catch (\Throwable $th) {

            flash(translate('Something went wrong! Please try again'))->error();
            return redirect()->back();
        }
    }
}
