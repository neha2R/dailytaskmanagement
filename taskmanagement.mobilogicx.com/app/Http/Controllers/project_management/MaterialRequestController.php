<?php

namespace App\Http\Controllers\project_management;

use App\Http\Controllers\Controller;
use App\Models\MaterialRequest;
use Illuminate\Http\Request;

class MaterialRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = MaterialRequest::orderByDesc('created_at')->get();
        return view('admin.project_management.material_requests', compact('requests'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $materialRequest = MaterialRequest::find($id);

        if (!$materialRequest) {
            return response()->json(['status' => 404, 'message' => 'Material Request not found']);
        }

        $materialRequest->project_name = $materialRequest->project->project_name ?? "";
        $materialRequest->created_at_formatted = dateformat($materialRequest->created_at, 'd M Y');
        $materialRequest->user_name = $materialRequest->user->name ?? "";
        $materialRequest->site_name = $materialRequest->site->name ?? "";

        $products = $materialRequest->products->map(function ($product) {
            return [
                'product_name' => $product->product->name ?? "",
                'category_name' => $product->product->category->name ?? "",
                'quantity' => $product->quantity ?? "",
                'uom_name' => $product->product->uom->name ?? "",
            ];
        });

        $responseData = [
            'status' => 200,
            'request' => $materialRequest->toArray(),
            'products' => $products->toArray(),
        ];

        return response()->json($responseData);
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
        //
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
}
