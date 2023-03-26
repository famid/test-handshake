<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\PackageUser;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::paginate(20);
        return view('backend.package.index',compact('packages'));
    }

    public function create()
    {
        return view('backend.package.create');
    }

    public function edit($id)
    {
        $package = Package::find($id);
        return view('backend.package.edit',compact('package'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'name'             => 'required',
            'package_price'    => 'required',
            'product_limit'    => 'required',
            'warehouse_limit'  => 'required',
            'daraz_sync_limit' => 'required',
            'package_duration' => 'required'
        ],
        [
            'name.required'             => 'Package name is required',
            'package_price.required'    => 'Package name is required',
            'product_limit.required'    => 'Product limit is required',
            'warehouse_limit.required'  => 'Warehouse limit is required',
            'daraz_sync_limit.required' => 'Daraz sync limit is required',
            'package_duration.required' => 'Package Duration is required'
        ]);

        try {

            Package::create([
                'package_name'     => $request->name,
                'package_type'     => $request->package_type,
                'package_price'    => $request->package_price,
                'product_limit'    => $request->product_limit,
                'warehouse_limit'  => $request->warehouse_limit,
                'daraz_sync_limit' => $request->daraz_sync_limit,
                'package_duration' => $request->package_duration,
                'status'           => $request->status,
                'package_image'    => $request->package_image,
                'additional_packages' => isset($request->additional_info) && in_array(!null, $request->additional_info) ? json_encode(array_filter($request->additional_info)) : null
            ]);

            flash(translate('Package has been inserted successfully'))->success();
            return redirect()->route('package.index');

        } catch (\Throwable $th) {
            flash(translate('Something went wrong! Please try again'))->error();
            return redirect()->back();
        }
    }


    public function update(Request $request,$id)
    {
        try {

            $package = Package::find($id);
            $package->update([
                'package_name'     => $request->name,
                'package_type'     => $request->package_type,
                'package_price'    => $request->package_price,
                'product_limit'    => $request->product_limit,
                'warehouse_limit'  => $request->warehouse_limit,
                'daraz_sync_limit' => $request->daraz_sync_limit,
                'package_duration' => $request->package_duration,
                'status'           => $request->status,
                'package_image'    => $request->package_image,
                'additional_packages' => isset($request->additional_info) && in_array(!null, $request->additional_info) ? json_encode(array_filter($request->additional_info)) : null
            ]);

            flash(translate('Package has been updated successfully'))->success();
            return redirect()->route('package.index');

        } catch (\Throwable $th) {

            flash(translate('Something went wrong! Please try again'))->error();
            return redirect()->back();
        }
    }


    public function destroy($id)
    {
        try {

            $package = Package::find($id);
            $package->delete();

            flash(translate('Package has been deleted successfully'))->success();
            return redirect()->route('package.index');
        } catch (\Throwable $th) {

            flash(translate('Something went wrong! Please try again'))->error();
            return redirect()->back();
        }
    }

}
