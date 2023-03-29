<?php

namespace App\Http\Controllers;

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

class LocationController extends Controller
{
    use UserTypeViewsTrait;
    use UserTypeRoutesTrait;

    const LOCATION_TABLE_PAGINATION_NUMBER = 2;

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request) {
        try {
            $locations = $this->buildLocationQuery($request)->paginate(self::LOCATION_TABLE_PAGINATION_NUMBER);
            $sort_search = $request->input('search');

            return $this->loadView('location.index', compact('locations', 'sort_search'));
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function buildLocationQuery(Request $request) {
        $search = $request->input('search');
        $userType = $request->input('user_type');

        $locations = Location::orderBy('created_at', 'desc')
            ->when(Auth::user()->user_type !== 'admin', function ($query) {
                $query->whereHas('warehouse', function ($subquery) {
                    $subquery->where('owner_id', Auth::id());
                });
            });

        if (isset($search) && trim($search) !== ''){
            $locations = $locations->where('name', 'like', '%'.$search.'%');
        }

        if (isset($userType) && trim($userType) !== '') {
            if ($userType === 'VENDOR') {
                $locations = $locations->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', '!=', Auth::id());
                });

            } else if ($userType === 'OWNER') {
                $locations = $locations->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', Auth::id());
                });
            }
        }

        return $locations;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(Request $request)
    {
        CoreComponentRepository::initializeCache();

        $vendorsWarehouses = Warehouse::select('id', 'name', 'code')
            ->where('owner_id', auth()->user()->id)
            ->get();

        if($request->input('id')) {
            if( !$vendorsWarehouses->contains('id', $request->input('id')) )
                abort(403);
        }

        return $this->loadView('location.create', ['warehouses' => $vendorsWarehouses, 'selectedWarehouse' => $request->input('id')]);
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
                'warehouse_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $created = Location::create([
                'name' => $request->name,
                'code' => $request->code,
                'warehouse_id' => $request->warehouse_id
            ]);

            if(!$created) {
                throw new \Exception('Failed to create Location!');
            }

            return $this->redirectToRoute('location.index')->with('success', translate('Location created successfully!'));

        } catch (Exception $e) {
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
            $location = Location::with('warehouse')
                ->findOrFail($id);

            if (!$this->hasAccess($location->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            $vendorsWarehouses = Warehouse::select('id', 'name', 'code')
                ->where('owner_id', auth()->user()->id)
                ->get();

            return $this->loadView('location.edit', ['warehouses' => $vendorsWarehouses, 'location' => $location]);
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
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $updated = Location::where('id', $id)
                ->update([
                    'name' => $request->name,
                    'code' => $request->code,
                    'warehouse_id' => $request->warehouse_id,
                ]);

            if(!$updated) {
                throw new Exception('Failed to update Location!');
            }

            return $this->redirectToRoute('location.index')
                ->with('success', translate('Location updated successfully!'));
        } catch (Exception $e) {

            return back()->with('error', translate($e->getMessage()));
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        try {
            $location = Location::findOrFail($id);

            if (!$this->hasAccess($location->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            if ($location->hasAreas()) {
                throw new Exception('Cannot delete location with associated areas.');
            }

            $location->delete();

            return $this->redirectToRoute('location.index')->with('success', 'Location deleted successfully.');
        } catch (ModelNotFoundException $e) {

            return back()->with('error', 'Location not found.');
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
