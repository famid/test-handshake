<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $product = Product::where('id', $id)
                ->firstOrFail();
            if($product->user_id != auth()->user()->id) {
                abort(403);
            }

            $ownWarehouses = Warehouse::where('owner_id', auth()->user()->id)
                ->get();

            $inventory = Inventory::with(['product', 'product_stock', 'product_owner', 'warehouse_owner'])
                ->where('product_owner_id', auth()->user()->id)
                ->orderBy('created_at', 'desc');

            if ($request->has('search')) {
                $search = $request->search;
                $inventory = $inventory->where('bin_location', 'like', '%'.$search.'%');
            }

            $inventory = $inventory->paginate(1);

            return view('frontend.inventory.show', compact(['ownWarehouses', 'product', 'inventory']));
        } catch (Exception $e) {
            return view('frontend.inventory.show')->with('error', $e->getMessage());
        }
    }
}
