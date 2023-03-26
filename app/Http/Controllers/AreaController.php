<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Location;
use App\Models\Warehouse;
use App\Traits\UserTypeRoutesTrait;
use App\Traits\UserTypeViewsTrait;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use CoreComponentRepository;
use Exception;
use Auth;

class AreaController extends Controller
{
    use UserTypeViewsTrait;
    use UserTypeRoutesTrait;

    const AREA_TABLE_PAGINATION_NUMBER = 2;

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $areas = $this->buildAreaQuery($request)->paginate(self::AREA_TABLE_PAGINATION_NUMBER);
            $sort_search = $request->input('search');

            return $this->loadView('area.index', compact('areas', 'sort_search'));
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function buildAreaQuery(Request $request) {
        $search = $request->input('search');
        $userType = $request->input('user_type');

        $areas = Area::orderBy('created_at', 'desc')
            ->when(Auth::user()->user_type !== 'admin', function ($query) {
                $query->whereHas('warehouse', function ($subquery) {
                    $subquery->where('owner_id', Auth::id());
                });
            });

        if (isset($search) && trim($search) !== ''){
            $areas = $areas->where('name', 'like', '%'.$search.'%');
        }

        if (isset($userType) && trim($userType) !== '') {
            if ($userType === 'VENDOR') {
                $areas = $areas->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', '!=', Auth::id());
                });

            } else if ($userType === 'OWNER') {
                $areas = $areas->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', Auth::id());
                });
            }
        }

        return $areas;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        CoreComponentRepository::initializeCache();

        $vendorsWarehouses = Warehouse::select('id', 'name', 'code')
            ->where('owner_id', auth()->user()->id)
            ->get();

        return $this->loadView('area.create', ['warehouses' => $vendorsWarehouses]);
    }

    public function getLocationsByWarehouse(Request $request)
    {
        $vendorsWarehouseLocations = Location::select('id', 'name', 'code')
            ->where('warehouse_id', $request->warehouse_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vendorsWarehouseLocations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'warehouse_id' => 'required|integer',
                'location_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $created = Area::create([
                'name' => $request->name,
                'code' => $request->code,
                'warehouse_id' => $request->warehouse_id,
                'location_id' => $request->location_id
            ]);

            if(!$created) {
                throw new \Exception('Failed to create Area!');
            }

            return $this->redirectToRoute('area.index')->with('success', translate('Area created successfully!'));

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
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
            $area = Area::with('location')
                ->findOrFail($id);

            if (!$this->hasAccess($area->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            $vendorsWarehouses = Warehouse::select('id', 'name', 'code')
                ->where('owner_id', auth()->user()->id)
                ->get();

            return $this->loadView('area.edit', ['warehouses' => $vendorsWarehouses, 'area' => $area]);
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
                'warehouse_id' => 'required|integer',
                'location_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $updated = Area::where('id', $id)
                ->update([
                    'name' => $request->name,
                    'code' => $request->code,
                    'warehouse_id' => $request->warehouse_id,
                    'location_id' => $request->location_id,
                ]);

            if(!$updated) {
                throw new Exception('Failed to update Area!');
            }

            return $this->redirectToRoute('area.index')
                ->with('success', translate('Area updated successfully!'));
        } catch (Exception $e) {

            return back()->with('error', translate($e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $area = Area::findOrFail($id);

            if (!$this->hasAccess($area->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            if ($area->hasShelves()) {
                throw new Exception('Cannot delete area with associated shelves.');
            }

            $area->delete();

            return $this->redirectToRoute('area.index')->with('success', 'area deleted successfully.');
        } catch (ModelNotFoundException $e) {

            return back()->with('error', 'area not found.');
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
