<?php

namespace App\Http\Controllers;

use App\Models\Cell;
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

class CellController extends Controller
{
    use userTypeViewsTrait;
    use UserTypeRoutesTrait;

    const CELL_TABLE_PAGINATION_NUMBER = 2;

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View|RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $cells = $this->buildCellQuery($request)->paginate(self::CELL_TABLE_PAGINATION_NUMBER);
            $sort_search = $request->input('search');

            return $this->loadView('cell.index', compact('cells', 'sort_search'));
        } catch (Exception $e) {

            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    private function buildCellQuery(Request $request) {
        $search = $request->input('search');
        $userType = $request->input('user_type');

        $cells = Cell::orderBy('created_at', 'desc')
            ->when(Auth::user()->user_type !== 'admin', function ($query) {
                $query->whereHas('warehouse', function ($subquery) {
                    $subquery->where('owner_id', Auth::id());
                });
            });

        if (isset($search) && trim($search) !== ''){
            $cells = $cells->where('name', 'like', '%'.$search.'%');
        }

        if (isset($userType) && trim($userType) !== '') {
            if ($userType === 'VENDOR') {
                $cells = $cells->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', '!=', Auth::id());
                });

            } else if ($userType === 'OWNER') {
                $cells = $cells->whereHas('warehouse', function ($query) {
                    $query->where('owner_id', Auth::id());
                });
            }
        }

        return $cells;
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

        return $this->loadView('cell.create', ['warehouses' => $vendorsWarehouses]);
    }

    public function getShelvesByArea(Request $request)
    {
        $vendorsAreaShelves = Shelf::select('id', 'name', 'code')
            ->where('area_id', $request->area_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vendorsAreaShelves
        ]);
    }

    public function getCellsByShelf(Request $request)
    {
        $vendorsShelfCells = Cell::select('id', 'name', 'code')
            ->where('shelf_id', $request->shelf_id)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $vendorsShelfCells
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
                'area_id' => 'required|integer',
                'shelf_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                throw new Exception($validator->errors()->first());
            }

            $created = Cell::create([
                'name' => $request->name,
                'code' => $request->code,
                'warehouse_id' => $request->warehouse_id,
                'location_id' => $request->location_id,
                'area_id' => $request->area_id,
                'shelf_id' => $request->shelf_id
            ]);

            if(!$created) {
                throw new Exception('Failed to create Cell!');
            }

            return redirect()->route('cell.index')->with('success', translate('Cell created successfully!'));
        } catch (Exception $e) {

            return back()->with('error', translate($e->getMessage()));
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
     * @return Application|Factory|View|RedirectResponse
     */
    public function edit(int $id)
    {
        try {
            $cell = Cell::with('shelf')
                ->findOrFail($id);

            if (!$this->hasAccess($cell->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            $vendorsWarehouses = Warehouse::select('id', 'name', 'code')
                ->where('owner_id', auth()->user()->id)
                ->get();

            return $this->loadView('cell.edit')->with(['warehouses' => $vendorsWarehouses, 'cell' => $cell]);
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
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'code' => 'required|string|max:255',
                'warehouse_id' => 'required|integer',
                'location_id' => 'required|integer',
                'area_id' => 'required|integer',
                'shelf_id' => 'required|integer'
            ]);

            if ($validator->fails()) {
                throw new \Exception($validator->errors()->first());
            }

            $cell = Cell::findOrFail($id);
            if( $cell->warehouse->owner_id != auth()->user()->id ) {
                abort(403);
            }

            $updated = Cell::where('id', $id)
                ->update([
                    'name' => $request->name,
                    'code' => $request->code,
                    'warehouse_id' => $request->warehouse_id,
                    'location_id' => $request->location_id,
                    'area_id' => $request->area_id,
                    'shelf_id' => $request->shelf_id
                ]);

            if(!$updated) {
                throw new \Exception('Failed to update Cell!');
            }

            return $this->redirectToRoute('cell.index')->with('success', translate('Cell updated successfully!'));

        } catch (\Exception $e) {
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
            $cell = Cell::findOrFail($id);

            if (!$this->hasAccess($cell->warehouse->owner_id)) {
                return back()->with('error', "Forbidden");
            }

            $cell->delete();

            return $this->redirectToRoute('cell.index')->with('success', 'cell deleted successfully.');
        } catch (ModelNotFoundException $e) {

            return back()->with('error', 'cell not found.');
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
