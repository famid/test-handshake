<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Shelf;
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

class ShelfController extends Controller
{
    use userTypeViewsTrait;
    use UserTypeRoutesTrait;

    const SHELF_TABLE_PAGINATION_NUMBER = 2;

    /**
     * @param Request $request
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $shelves = $this->buildShelfQuery($request)->paginate(self::SHELF_TABLE_PAGINATION_NUMBER);
            $sort_search = $request->input('search');

            return $this->loadView('shelf.index', compact('shelves', 'sort_search'));
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function buildShelfQuery(Request $request) {
        $search = $request->input('search');
        $userType = $request->input('user_type');

        $shelves = Shelf::orderBy('created_at', 'desc')
            ->when(Auth::user()->user_type !== 'admin', function ($query) {
                $query->whereHas('warehouse', function ($subquery) {
                    $subquery->where('owner_id', Auth::id());
                });
            });

        if (isset($search) && trim($search) !== ''){
            $shelves = $shelves->where('name', 'like', '%'.$search.'%');
        }

        if (isset($userType) && trim($userType) !== '') {
            if ($userType === 'VENDOR') {
                $shelves = $shelves->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', '!=', Auth::id());
                });

            } else if ($userType === 'OWNER') {
                $shelves = $shelves->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', Auth::id());
                });
            }
        }

        return $shelves;
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

        $selectedArea = null;
        if($request->input('id')) {
            $selectedArea = Area::with('location')
                ->where('id', $request->input('id'))
                ->firstOrFail();

            if( !$vendorsWarehouses->contains('id', $selectedArea->location->warehouse->id) )
                abort(403);
        }

        return $this->loadView('shelf.create', ['warehouses' => $vendorsWarehouses, 'selectedArea' => $selectedArea]);
    }

    public function getAreasByLocation(Request $request)
    {
        $vendorsLocationAreas = Area::select('id', 'name', 'code')
            ->where('location_id', $request->location_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vendorsLocationAreas
        ]);
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
                'code' => 'required|string|max:255',
                'warehouse_id' => 'required|integer',
                'location_id' => 'required|integer',
                'area_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $created = Shelf::create([
                'name' => $request->name,
                'code' => $request->code,
                'warehouse_id' => $request->warehouse_id,
                'location_id' => $request->location_id,
                'area_id' => $request->area_id
            ]);

            if(!$created) {
                throw new \Exception('Failed to create Shelf!');
            }

            return $this->redirectToRoute('shelf.index')->with('success', translate('Shelf created successfully!'));

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
            $shelf = Shelf::with('area')
                ->findOrFail($id);

            if (!$this->hasAccess($shelf->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            $vendorsWarehouses = Warehouse::select('id', 'name', 'code')
                ->where('owner_id', auth()->user()->id)
                ->get();

            return $this->loadView('shelf.edit', ['warehouses' => $vendorsWarehouses, 'shelf' => $shelf]);
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
                'area_id' => 'required|integer',
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $updated = Shelf::where('id', $id)
                ->update([
                    'name' => $request->name,
                    'code' => $request->code,
                    'warehouse_id' => $request->warehouse_id,
                    'location_id' => $request->location_id,
                    'area_id' => $request->area_id,
                ]);

            if(!$updated) {
                throw new Exception('Failed to update Shelf!');
            }

            return $this->redirectToRoute('shelf.index')
                ->with('success', translate('Shelf updated successfully!'));
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
            $shelf = Shelf::findOrFail($id);

            if (!$this->hasAccess($shelf->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            if ($shelf->hasCells()) {
                throw new Exception('Cannot delete shelf with associated cells.');
            }

            $shelf->delete();

            return $this->redirectToRoute('shelf.index')->with('success', 'shelf deleted successfully.');
        } catch (ModelNotFoundException $e) {

            return back()->with('error', 'shelf not found.');
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
