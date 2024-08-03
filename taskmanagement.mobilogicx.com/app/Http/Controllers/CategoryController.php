<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Categories::whereNull('parent_id')->get();
        $sub_categories = Categories::whereNotNull('parent_id')->with('category')->get();
        return view('admin.inv_management.categories', compact('categories', 'sub_categories'));
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
        if ($request->category_id) {
            $request->validate([
                'category_id' => 'required',
                'name' => 'required'
            ]);
            $create = Categories::create([
                'name' => $request->name,
                'parent_id' => $request->category_id
            ]);
            if ($create) {
                return redirect()->back()->with('success', 'Sub category added successfully');
            }
        } else {
            $request->validate([
                'name' => 'required'
            ]);
            $create = Categories::create(['name' => $request->name]);
            if ($create) {
                return redirect()->back()->with('success', 'Category added successfully');
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Categories::find($id);
        return response()->json(['status' => 200, 'data' => $data]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
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
        $category = Categories::find($request->id);
        if ($request->category_id) {
            $request->validate([
                'category_id' => 'required',
                'name' => 'required'
            ]);
            $create = $category->update([
                'name' => $request->name,
                'parent_id' => $request->category_id
            ]);
            if ($create) {
                return redirect()->back()->with('success', 'Sub category updated successfully');
            }
        } else {
            $request->validate([
                'name' => 'required'
            ]);
            $create = $category->update(['name' => $request->name]);
            if ($create) {
                return redirect()->back()->with('success', 'Category updated successfully');
            }
        }
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
    public function showSubCategory($id)
    {
        $data=Categories::where('parent_id',$id)->get();
        return response()->json(['status'=>200,'data'=>$data]);
    }

    public function change_status(Request $request)
    {
        Categories::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }
}
