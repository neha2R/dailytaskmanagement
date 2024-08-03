<?php

namespace App\Http\Controllers;

use App\Models\Companie;
use App\Models\Companies_categories;
use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $companies=Companie::orderBy('name')->get();
        return view('admin.inv_management.companies',compact('companies'));
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
        $request->validate([
            'name'=>'required',
        ]);
        $create = Companie::create([
            'name'=>$request->name,
            'portfolio'=>$request->portfolio,
            'contact_no'=>$request->contact_no,
            'email'=>$request->email ?? null, 
            'address'=>$request->address
        ]);
        if ($data=$request->categories_id) {
            foreach ($data as $key => $value) {
                Companies_categories::create([
                    'company_id'=>$create->id,
                    'category_id'=>$value
                ]);
            }
        }
        return redirect()->back()->with('success','Company added successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data=Companie::find($id);
        $categories=Companies_categories::where('company_id',$id)->pluck('category_id');

        return response()->json(['status'=>200,'data'=>$data,'categories'=>$categories]);
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
        $request->validate([
            'name'=>'required',
        ]);
        $company=Companie::find($request->id);
        $update = $company->update([
            'name'=>$request->name,
            'portfolio'=>$request->portfolio,
            'contact_no'=>$request->contact_no,
            'email'=>$request->email ?? null, 
            'address'=>$request->address
        ]);
        if ($data=$request->categories_id) {
            foreach ($data as $key => $value) {
                $remove_categories=Companies_categories::where('company_id',$request->id)->whereNotIn('category_id',$data)->delete();
                $CreateUpdate_categories=Companies_categories::updateOrCreate(['category_id'=>$value,'company_id'=>$request->id]);
            }
        }else{
            $remove_categories=Companies_categories::where('company_id',$request->id)->delete();

        }
        return redirect()->back()->with('success','Company updated successfully');
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

    public function change_status(Request $request)
    {
        Companie::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
}
