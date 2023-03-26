<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Traits\UserTypeRoutesTrait;
use App\Traits\UserTypeViewsTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use CoreComponentRepository;
use Exception;
use Auth;

class WarehouseController extends Controller
{
    use UserTypeViewsTrait;
    use UserTypeRoutesTrait;

    const WAREHOUSE_TABLE_PAGINATION_NUMBER = 2;

    /**
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request) {
        try {
            $warehouses = $this->buildWarehouseQuery($request)->paginate(self::WAREHOUSE_TABLE_PAGINATION_NUMBER);
            $sort_search = $request->input('search');

            return $this->loadView('warehouse.index', compact('warehouses', 'sort_search'));
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function buildWarehouseQuery(Request $request)
    {
        $search = $request->input('search');
        $userType = $request->input('user_type');


        $warehouses = Warehouse::orderBy('created_at', 'desc')
            ->when(Auth::user()->user_type !== 'admin', function ($query) {
                $query->where('owner_id', Auth::id());
            });

        if (isset($search) && trim($search) !== ''){
            $warehouses = $warehouses->where('name', 'like', '%'.$search.'%');
        }

        if (isset($userType) && trim($userType) !== '') {
            if ($userType === 'VENDOR') {
                $warehouses = $warehouses->where('owner_id', '!=', Auth::id());

            } else if ($userType === 'OWNER') {
                $warehouses = $warehouses->where('owner_id', Auth::id());
            }
        }

        return $warehouses;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        CoreComponentRepository::initializeCache();

        return $this->loadView('warehouse.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $created = Warehouse::create([
                'name' => $request->name,
                'code' => $request->code,
                'owner_id' => auth()->user()->id
            ]);

            if(!$created) {
                throw new \Exception('Failed to create Warehouse!');
            }

            return $this->redirectToRoute('warehouse.index')
                ->with('success', translate('Warehouse created successfully!'));
        } catch (\Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return Application|Factory|RedirectResponse|View
     */
    public function edit(int $id)
    {
        try {
            $warehouse = Warehouse::findOrFail($id);

            if (!$this->hasAccess($warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            return $this->loadView('warehouse.edit', ['warehouse' => $warehouse]);
        } catch (Exception $e) {

            return back()->with('error', translate($e->getMessage()));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id): RedirectResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $updated = Warehouse::where('id', $id)
                ->update([
                    'name' => $request->name,
                    'code' => $request->code,
                ]);

            if(!$updated) {
                throw new Exception('Failed to update Warehouse!');
            }

            return $this->redirectToRoute('warehouse.index')
                ->with('success', translate('Warehouse updated successfully!'));
        } catch (Exception $e) {

            return back()->with('error', translate($e->getMessage()));
        }
    }

    /**
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse {
        try {
            $warehouse = Warehouse::findOrFail($id);

            if (!$this->hasAccess($warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            if ($warehouse->hasLocations()) {
                throw new Exception('Cannot delete warehouse with associated locations.');
            }

            $warehouse->delete();

            return $this->redirectToRoute('warehouse.index')->with('success', 'Warehouse deleted successfully.');
        } catch (ModelNotFoundException $e) {

            return back()->with('error', 'Warehouse not found.');
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param int $ownerId
     * @return bool
     */
    private function hasAccess(int $ownerId): bool {
        return  $ownerId === Auth::id();
    }
}
