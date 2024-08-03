<?php

namespace App\Http\Controllers;

use App\Models\VehicleManufacturer;
use App\Models\VehicleModel;
use Illuminate\Http\Request;

class VehicleMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vehicle_mf=VehicleManufacturer::orderBy('name')->get();
        $vehicle_models=VehicleModel::orderBy('name')->get();
        return view('admin.vehicle_master.vehicles',compact('vehicle_mf','vehicle_models'));
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
        // add mf
        $request->validate([
            'name'=>'required|max:120',
            'sort_name'=>'required|max:60',
        ]);
        VehicleManufacturer::create([
            'name'=>$request->name,
            'sort_name'=>$request->sort_name
        ]);
        return redirect()->back()->with('success','Vehicle manufacturer added successfully');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=VehicleManufacturer::find($id);
        return response()->json(['status'=>200,'data'=>$data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        // edit manufacturer
        $request->validate([
            'name'=>'required|max:120',
            'sort_name'=>'required|max:60',
        ]);
        $vehicle_mf=VehicleManufacturer::find($request->id);
        $vehicle_mf->update([
            'name'=>$request->name,
            'sort_name'=>$request->sort_name
        ]);
        return redirect()->back()->with('success','Vehicle manufacturer updated successfully');        
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

    public function showVehicleModel($id)
    {
        $data=VehicleModel::find($id);
        return response()->json(['status'=>200,'data'=>$data]);
    }

    public function storeVehicleModel(Request $request)
    {
        // return $request;
        $request->validate([
            'name'=>'required|max:120',
            'manufacturer_id'=>'required',
            'NoOfbatteries'=>'required|max:50',
            'NoOfTyres'=>'required|max:50',
            'fule_type'=>'required',
        ]);
        VehicleModel::create([
            'name'=>$request->name,
            'manufacturer_id'=>$request->manufacturer_id,
            'no_of_batteries'=>$request->NoOfbatteries,
            'no_of_tyres'=>$request->NoOfTyres,
            'tyre_type'=>$request->tyre_type ?? '',
            'fule_type'=>$request->fule_type,
        ]);
        return redirect()->back()->with('success','Vehicle model added successfully');
    }

    public function updateVehicleModel(Request $request)
    {
        $request->validate([
            'name'=>'required|max:120',
            'manufacturer_id'=>'required',
            'NoOfbatteries'=>'required|max:50',
            'NoOfTyres'=>'required|max:50',
            'fule_type'=>'required',
        ]);
        $vehicle_model=VehicleModel::find($request->id);
        $vehicle_model->update([
            'name'=>$request->name,
            'manufacturer_id'=>$request->manufacturer_id,
            'no_of_batteries'=>$request->NoOfbatteries,
            'no_of_tyres'=>$request->NoOfTyres,
            'tyre_type'=>$request->tyre_type ?? '',
            'fule_type'=>$request->fule_type,
        ]);
        return redirect()->back()->with('success','Vehicle model updated successfully');   
    }

    public function changeVMFstatus(Request $request)
    {
        VehicleManufacturer::find($request->id)->update(['is_active'=>$request->status === 'true'? true: false]);
        return response()->json(['status'=>200, 'message'=>'Status changed successfully']);
    }

    public function changeVMstatus(Request $request)
    {
        VehicleModel::find($request->id)->update(['is_active'=>$request->status === 'true'? true: false]);
        return response()->json(['status'=>200, 'message'=>'Status changed successfully']);
    }

}
