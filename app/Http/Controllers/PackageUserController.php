<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageUser;
use App\Models\User;
use Illuminate\Http\Request;

class PackageUserController extends Controller
{
    public function index()
    {
        $package_users = PackageUser::paginate(20);
        return view('backend.package_users.index',compact('package_users'));
    }


    public function create()
    {
        $packages = Package::where('status',1)->get();
        $users    = User::whereIn('user_type',['seller','customer'])->get();

        return view('backend.package_users.create',compact('packages','users'));
    }

    public function edit($id)
    {
        $package_user = PackageUser::find($id);
        $users        = User::whereIn('user_type',['seller','customer'])->get();

        if ($package_user->User->user_type == 'customer') {
            $type = 'seller';
        }elseif ($package_user->User->user_type == 'seller') {
            $type = 'vendor';
        }

        $packages     = Package::where('package_type',$type)->get();

        return view('backend.package_users.edit',compact('package_user','packages','users'));
    }


    public function store(Request $request)
    {
        try {


        } catch (\Throwable $th) {


        }
    }


    public function packageByUser($id)
    {
        try {

            $user = User::find($id);
            if ($user->user_type == 'customer') {
                $type = 'seller';
            }elseif ($user->user_type == 'seller') {
                $type = 'vendor';
            }

            $data = [];
            $package = Package::where('package_type',$type)->where('status',1)->get();
            $view    = view('backend.inc.package_by_user',compact('package'))->render();

            return response()->json([
                'result' => true,
                'view'   => $view
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'result'  => false,
                'message' => 'Something went wrong! Please try again'
            ]);
        }
    }
}
