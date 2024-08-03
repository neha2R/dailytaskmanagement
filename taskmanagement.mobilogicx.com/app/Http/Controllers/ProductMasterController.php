<?php

namespace App\Http\Controllers;

use App\Models\Categories;
use App\Models\ProductMaster;
use App\Models\Products;
use Illuminate\Http\Request;

class ProductMasterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = ProductMaster::orderBy('name')->get();
        return view('admin.inv_management.product_master', compact('products'));
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
        // Validation rules with custom error messages
        $request->validate([
            'name' => 'required|unique:product_masters,name',
            'company_id' => 'required',
            'sub_category_id' => 'required',
            'uom' => 'required',
        ], [
            'name.unique' => 'The product name is already taken',
        ]);

        // Retrieve sub-category details
        $subCategory = Categories::find($request->sub_category_id);

        // Create a new ProductMaster instance
        $product = ProductMaster::create([
            'name' => $request->name,
            'category_id' => $subCategory->parent_id,
            'sub_category_id' => $subCategory->id,
            'company_id' => $request->company_id,
            'uom_id' => $request->uom,
            'min_stock_warehouse' => $request->min_stock_warehouses,
            'min_stock_depo' => $request->min_stock_depo,
        ]);

        // Redirect with a success message
        return redirect()->back()->with('success', 'Product "' . $product->name . ' " added successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = ProductMaster::find($id);
        return response()->json(['status' => 200, 'data' => $product]);
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
        $id=$request->id;
        // Validation rules with custom error messages
        $request->validate([
            'id' => 'required',
            'name' => 'required|unique:product_masters,name,' . $id,
            'company_id' => 'required',
            'sub_category_id' => 'required|exists:categories,id', // Add validation for existence of sub_category_id in the categories table
            'uom' => 'required',
        ], [
            'name.unique' => 'The product name is already taken',
            'sub_category_id.exists' => 'Invalid sub-category selected.',
        ]);

        // Retrieve sub-category details
        $subCategory = Categories::find($request->sub_category_id);

        // Handle the case where sub-category is not found
        if (!$subCategory) {
            return redirect()->back()->with('error', 'Invalid sub-category selected.');
        }

        // Find the product by ID
        $product = ProductMaster::findOrFail($id);

        // Update product details
        $product->update([
            'name' => $request->name,
            'category_id' => $subCategory->parent_id,
            'sub_category_id' => $subCategory->id,
            'company_id' => $request->company_id,
            'uom_id' => $request->uom,
            'min_stock_warehouse' => $request->min_stock_warehouses,
            'min_stock_depo' => $request->min_stock_depo,
        ]);

        // Redirect with a success message
        return redirect()->back()->with('success', 'Product "' . $product->name . '" updated successfully.');
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
        ProductMaster::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }

    // function for check product name is unique
    public function checkUniqueProductName(Request $request)
    {
        $productName = $request->input('name');
        // Check if the product name is unique
        $isUnique = !ProductMaster::where('name', $productName)->exists();

        return response()->json(['unique' => $isUnique]);
    }
}
