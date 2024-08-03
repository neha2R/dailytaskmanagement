<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Division;
use App\Models\HistoryMapUnmapDivSubDivSite;
use App\Models\Site;
use App\Models\SubDivision;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DivisionSiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all divisions with associated sub-divisions
        $divisions = Division::orderByDesc('created_at')->with('sub_divisions')->get();

        // Retrieve unmapped divisions
        $unMappedDivisions = Division::whereNull('user_id')->orderBy('id', 'desc')->get(['id', 'name']);

        // Pass data to the view
        return view('admin.divisions.index', compact('divisions', 'unMappedDivisions'));
    }

    public function subDivision()
    {
        // Retrieve all sub-divisions
        $subDivisions = SubDivision::orderByDesc('created_at')->get();

        // Retrieve active divisions
        $divisions = Division::where('is_active', true)->get();

        // Retrieve unmapped sub-divisions
        $unMappedSubDivisions = SubDivision::whereNull('user_id')->orderBy('id', 'desc')->get(['id', 'name']);

        // Pass data to the view
        return view('admin.subdivisions.index', compact('subDivisions', 'unMappedSubDivisions', 'divisions'));
    }

    public function site()
    {
        // Retrieve all sites
        $sites = Site::orderByDesc('created_at')->get();

        // Retrieve unmapped sites
        $unMappedSites = Site::whereNull('user_id')->orderBy('id', 'desc')->get(['id', 'name']);

        // Pass data to the view
        return view('admin.sites.index', compact('sites', 'unMappedSites'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'division_name' => 'required|string|max:50',
            'division_city' => 'nullable|string|max:30',
            'division_address' => 'nullable|string|max:100',
        ]);

        // Generate a UUID for the new division
        $uuid = Str::uuid();

        // Create and save the division
        $division = Division::create([
            'id' => $uuid,
            'name' => $validatedData['division_name'],
            'city' => $validatedData['division_city'],
            'address' => $validatedData['division_address'],
            'user_id' => $request->input('division_head_id', null),
        ]);

        // Redirect to the appropriate page
        return redirect()->route('admin.divisions-sites.index')
            ->with('success', 'Division created successfully');
    }
    public function subDivStore(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'sub_div_name' => 'required|string|max:50',
            'division_id' => 'required|exists:divisions,id',
            'sub_div_city' => 'required|string|max:30',
            'sub_div_address' => 'required|string|max:100',
            'sub_division_head_id' => 'nullable|exists:users,id',
        ]);

        // Generate a UUID for the new sub-division
        $uuid = Str::uuid();

        // Create and save the sub-division
        $subDivision = SubDivision::create([
            'id' => $uuid,
            'name' => $validatedData['sub_div_name'],
            'city' => $validatedData['sub_div_city'],
            'address' => $validatedData['sub_div_address'],
            'user_id' => $validatedData['sub_division_head_id'] ?? null,
            'division_id' => $validatedData['division_id'],
        ]);

        // Redirect to the appropriate page
        return redirect()->route('admin.subDivisions')
            ->with('success', 'Sub Division created successfully');
    }
    public function siteStore(Request $request)
    {
        // return $request;
        // Validate the request data
        $validatedData = $request->validate([
            'site_name' => 'required|string|max:50',
            'site_city' => 'required|string|max:30',
            'site_address' => 'required|string|max:100',
            'site_head_id' => 'nullable|exists:users,id',
            'sub_division_id' => 'required|exists:sub_divisions,id'
        ]);

        // Generate a UUID for the new division
        $uuid = Str::uuid();

        // Create and save the division
        $site = Site::create([
            'id' => $uuid,
            'name' => $validatedData['site_name'],
            'city' => $validatedData['site_city'],
            'address' => $validatedData['site_address'],
            'user_id' => $validatedData['site_head_id'] ?? null,
            'sub_division_id' => $validatedData['sub_division_id'],
        ]);

        // Redirect to the appropriate page
        return redirect()->route('admin.sites')
            ->with('success', 'Site created successfully');
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $division = Division::find($id);
        if ($division) {
            return response()->json(['status' => 200, 'division' => $division]);
        }
        return response()->json(['status' => 404, 'division' => null]);
    }
    public function subDivEdit($id)
    {
        $data = SubDivision::find($id);
        if ($data) {
            return response()->json(['status' => 200, 'sub_division' => $data]);
        }
        return response()->json(['status' => 404, 'sub_division' => null]);
    }
    public function siteEdit($id)
    {
        $data = Site::find($id);
        if ($data) {
            return response()->json(['status' => 200, 'site' => $data]);
        }
        return response()->json(['status' => 404, 'site' => null]);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // return $request;
        // Validate the request data
        $validatedData = $request->validate([
            'division_name' => 'required|string|max:50',
            'division_city' => 'nullable|string|max:30',
            'division_address' => 'nullable|string|max:100',
        ]);

        // Generate a UUID for the new division
        $division = Division::find($request->id);
        if ($division) {
            // Update the division
            $update = $division->update([
                'name' => $validatedData['division_name'],
                'city' => $validatedData['division_city'],
                'address' => $validatedData['division_address'],
                // 'user_id' => $request->input('division_head_id', null),
            ]);
            // Redirect to the appropriate page
            return redirect()->route('admin.divisions-sites.index')
                ->with('success', 'Division Updated successfully');
        }
        return redirect()->route('admin.divisions-sites.index')
            ->with('error', 'division is not found');
    }
    public function subDivUpdate(Request $request)
    {
        // return $request;
        // Validate the request data
        $validatedData = $request->validate([
            'id' => 'required',
            'sub_div_name' => 'required|string|max:50',
            'division_id' => 'required|exists:divisions,id',
            'sub_div_city' => 'required|string|max:30',
            'sub_div_address' => 'required|string|max:100',
            // 'sub_division_head_id' => 'nullable|exists:users,id',
        ]);

        // Generate a UUID for the new division
        $division = SubDivision::find($request->id);
        if ($division) {
            // Update the division
            $update = $division->update([
                'name' => $validatedData['sub_div_name'],
                'city' => $validatedData['sub_div_city'],
                'address' => $validatedData['sub_div_address'],
                // 'user_id' => $validatedData['sub_division_head_id'] ?? null,
                'division_id' => $validatedData['division_id'],
            ]);
            // Redirect to the appropriate page
            return redirect()->route('admin.subDivisions')
                ->with('success', 'Sub Division Updated successfully');
        }
        return redirect()->route('admin.subDivisions')
            ->with('error', 'Sub division is not found');
    }
    public function siteUpdate(Request $request)
    {
        // return $request;
        // Validate the request data
        $validatedData = $request->validate([
            'id' => 'required',
            'site_name' => 'required|string|max:50',
            'site_city' => 'required|string|max:30',
            'site_address' => 'required|string|max:100',
            'site_head_id' => 'nullable|exists:users,id',
            'sub_division_id' => 'required|exists:sub_divisions,id'
        ]);

        // Generate a UUID for the new division
        $division = Site::find($request->id);
        if ($division) {
            // Update the division
            $update = $division->update([
                'name' => $validatedData['site_name'],
                'city' => $validatedData['site_city'],
                'address' => $validatedData['site_address'],
                // 'user_id' => $validatedData['site_head_id'] ?? null,
                'sub_division_id' => $validatedData['sub_division_id'],
            ]);
            // Redirect to the appropriate page
            return redirect()->route('admin.sites')
                ->with('success', 'Site Updated successfully');
        }
        return redirect()->route('admin.sites')
            ->with('error', 'Site is not found');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    function divStatus(Request $request)
    {
        Division::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
    function subDivStatus(Request $request)
    {
        SubDivision::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
    function siteStatus(Request $request)
    {
        Site::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }

    function maping(Request $request)
    {
        // Define validation rules
        $rules = [
            'mapping_type' => 'required|in:division,subdivision,site',
            'user_id' => 'required',
            'map_ids' => 'required|array',
        ];

        // Validate the request data
        $validatedData = $request->validate($rules);

        try {
            // Begin a transaction to ensure data consistency
            DB::beginTransaction();

            // Fetch the appropriate source_type_id based on the mapping type
            $source_type_id = getProjectManagementTypeBySlug($validatedData['mapping_type']);

            if (!$source_type_id) {
                // If the source type is not found, gracefully handle the situation
                return redirect()->back()->with('error', 'An unexpected error occurred. Please contact the system administrator for assistance.');
            }

            // Loop through each map_id and create a separate record
            foreach ($validatedData['map_ids'] as $mapId) {
                // Prepare data for insertion
                $dataToInsert = [
                    'user_id' => $validatedData['user_id'],
                    'date' => now(),
                    'action' => 'map',
                    'source_id' => $mapId,
                    'source_type_id' => $source_type_id
                ];

                if ($validatedData['mapping_type'] === 'division') {
                    // Record the mapping in the history log
                    HistoryMapUnmapDivSubDivSite::create($dataToInsert);
                    // Update the Division record with the user_id
                    Division::find($mapId)->update(['user_id' => $dataToInsert['user_id']]);
                } elseif ($validatedData['mapping_type'] === 'subdivision') {
                    // Record the mapping in the history log
                    HistoryMapUnmapDivSubDivSite::create($dataToInsert);
                    // Update the sub Division record with the user_id
                    SubDivision::find($mapId)->update(['user_id' => $dataToInsert['user_id']]);
                } elseif ($validatedData['mapping_type'] === 'site') {
                    // Record the mapping in the history log
                    HistoryMapUnmapDivSubDivSite::create($dataToInsert);
                    // Update the sub Division record with the user_id
                    Site::find($mapId)->update(['user_id' => $dataToInsert['user_id']]);
                }
            }

            // Commit the transaction if all operations succeed
            DB::commit();

            // Redirect back with a success message
            return redirect()->back()->with('success', 'The mapping was successfully completed.');
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction
            DB::rollback();
            // Log the exception for further analysis
            Log::error($e);

            // Redirect back with an error message
            return redirect()->back()->with('error', 'An unexpected error occurred. Please contact the system administrator for assistance.');
        }
    }
    public function mappingHistory($id, $type)
    {
        try {
            // Define the eloquent model based on the mapping type
            $historyModel = HistoryMapUnmapDivSubDivSite::where('source_id', $id);

            switch ($type) {
                case 'division':
                    $data = $historyModel->with(['division', 'user'])->orderBy('id', 'desc')->get();
                    break;
                case 'subdivision':
                    $data = $historyModel->with(['subdivision', 'user'])->orderBy('id', 'desc')->get();
                    break;
                case 'site':
                    $data = $historyModel->with(['site', 'user'])->orderBy('id', 'desc')->get();
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid mapping type provided.');
            }

            if ($data->count()) {
                foreach ($data as $record) {
                    // Format date to 'd/m/Y'
                    $record->date = dateformat($record->date, 'd/m/Y');
                }

                return response()->json(['data' => $data, 'status' => 200]);
            }

            return response()->json(['data' => []]);
        } catch (\Exception $e) {
            // Log the exception or handle it accordingly
            return response()->json(['error' => 'An error occurred while fetching mapping history.'], 500);
        }
    }
    public function unmap($id, $type)
    {
        try {
            // Start a database transaction
            DB::beginTransaction();
            switch ($type) {
                case 'division':
                    $mappingEntry = HistoryMapUnmapDivSubDivSite::where(['action' => 'map', 'source_id' => $id, 'source_type_id' => getProjectManagementTypeBySlug('division')])->latest()->first();

                    break;
                case 'subdivision':
                    $mappingEntry = HistoryMapUnmapDivSubDivSite::where(['action' => 'map', 'source_id' => $id, 'source_type_id' => getProjectManagementTypeBySlug('subdivision')])->latest()->first();

                    break;
                case 'site':
                    $mappingEntry = HistoryMapUnmapDivSubDivSite::where(['action' => 'map', 'source_id' => $id, 'source_type_id' => getProjectManagementTypeBySlug('site')])->latest()->first();
                    break;
                default:
            }
            // Get the latest mapping entry
            if ($mappingEntry) {
                // Prepare data for unmapping
                $dataToInsert = [
                    'user_id' => $mappingEntry->user_id,
                    'date' => now(),
                    'action' => 'unmap',
                    'source_id' => $mappingEntry->source_id,
                    'source_type_id' => $mappingEntry->source_type_id
                ];

                // Record the unmapping in the history log based on the type
                switch ($type) {
                    case 'division':
                        HistoryMapUnmapDivSubDivSite::create($dataToInsert);
                        Division::find($mappingEntry->source_id)->update(['user_id' => null]);
                        break;
                    case 'subdivision':
                        HistoryMapUnmapDivSubDivSite::create($dataToInsert);
                        SubDivision::find($mappingEntry->source_id)->update(['user_id' => null]);
                        break;
                    case 'site':
                        HistoryMapUnmapDivSubDivSite::create($dataToInsert);
                        Site::find($mappingEntry->source_id)->update(['user_id' => null]);
                        break;
                    default:
                        return redirect()->back()->with('error', 'Invalid mapping type provided.');
                }

                // Commit the transaction if everything is successful
                DB::commit();

                // Return a success response
                return redirect()->back()->with('success', 'Unmapped successfully');
            } else {
                return redirect()->back()->with('error', 'Please Mapped first');
            }
        } catch (\Exception $e) {
            // If any exception occurs, rollback the transaction and handle the exception
            DB::rollback();

            // Log the exception or display a generic error message
            dd($e);
            return redirect()->back()->with('error', 'An error occurred while saving data ');
        }
    }
}
