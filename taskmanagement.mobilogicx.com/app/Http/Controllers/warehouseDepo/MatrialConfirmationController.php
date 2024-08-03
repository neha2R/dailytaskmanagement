<?php

namespace App\Http\Controllers\warehouseDepo;

use App\Http\Controllers\Controller;
use App\Models\Consignement;
use App\Models\ConsignementProducts;
use App\Models\Consignment;
use App\Models\DeliveryChallan;
use App\Models\Products;
use App\Models\WhDpMapedUser;
use App\Models\WhDpMappedVehicles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MatrialConfirmationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mappedWarehouseDepos = getMappedUserData();
        if ($mappedWarehouseDepos->isEmpty()) {
            return redirect()->back()->with('error', 'Oops! It seems you haven\'t been assigned to a warehouse and depot.');
        }

        $consignments = $this->getConsignments($mappedWarehouseDepos);
   
        return view($this->getViewName($mappedWarehouseDepos->first()), ['data' => $consignments]);
    }


    private function getConsignments($mappedWarehouseDepos)
    {
        $query = Consignment::withCount('products')
            ->orderBy('id', 'desc');
        $warehouseIds = $mappedWarehouseDepos->pluck('warehouse_id')->filter();
        $depotIds = $mappedWarehouseDepos->pluck('depo_id')->filter();
        if ($mappedWarehouseDepos->first()->warehouse_id !== null) {
            $query->whereIn('destination_source_id', $warehouseIds )->where('destination_source_type_id',getInventoryTypeBySlug('warehouse'));
        } elseif ($depotIds->first() !== null) {
            $query->whereIn('destination_source_id', $depotIds )->where('destination_source_type_id',getInventoryTypeBySlug('depot'));
        }

        return $query->get();
    }

    private function getViewName($mappedUser)
    {
        return ($mappedUser->warehouse_id !== null) ? 'warehouse_head.consignements' : 'depot_head.consignements';
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
        $data = Consignment::find($id);
        $data->con_num = env('PrefixCon') . $data->id;
        $data->delivery_by_date = dateformat($data->delivery_by_date, 'd M Y');
        $data->origin_location = $data->origin_source()->name;
        $data->destination_location = $data->destination_source()->name;

        $products = $data->products->map(function ($product) {
            return [
                'product_name' => $product->product->name ?? "",
                'category_name' => $product->product->category->name ?? "",
                'quantity' => $product->quantity ?? "",
                'uom_name' => $product->product->uom->name ?? "",
            ];
        });
        return response()->json(['status' => 200, 'con' => $data, 'products' => $products]);
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

    public function checkout($con_id)
    {
        $mappedWarehouseDepos = getMappedUserData();
        if ($mappedWarehouseDepos->isEmpty()) {
            return redirect()->back()->with('error', 'Oops! It seems you haven\'t been assigned to a warehouse and depot.');
        }

        $consignment = Consignment::find($con_id);
        $data = [
            'consignment' => $consignment
        ];

        if ($mappedWarehouseDepos->first()->warehouse_id !== null) {
            return view('warehouse_head.consignements_checkout', $data);
        } elseif ($mappedWarehouseDepos->first()->depo_id !== null) {
            return view('depot_head.consignements_checkout', $data);
        }
    }
}
