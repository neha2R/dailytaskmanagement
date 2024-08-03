<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class VendorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $vendors = Vendor::orderBy('name')->get();
        return view('admin.inv_management.vendors', compact('vendors'));
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
            'name' => 'required'
        ]);
        $create = Vendor::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'address' => $request->address,
        ]);
        return redirect()->back()->with('success', 'Vendor created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Vendor::find($id);
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
            'name' => 'required'
        ]);
        $vendor = Vendor::find($request->id);
        $vendor->update([
            'name' => $request->name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'address' => $request->address,
        ]);
        return redirect()->back()->with('success', 'Vendor updated successfully');
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
        Vendor::find($request->id)->update(['is_active' => $request->status === 'true' ? true : false]);
        return response()->json(['status' => 200, 'message' => 'Status changed successfully']);
    }


    public function bulkUpload(Request $request)
    {
        $this->validate($request, [
            'uploaded_file' => 'required'
        ]);
        $the_file = $request->file('uploaded_file');
        try {
            $spreadsheet = IOFactory::load($the_file->getRealPath());
            $sheet        = $spreadsheet->getActiveSheet();
            $row_limit    = $sheet->getHighestDataRow();
            $column_limit = $sheet->getHighestDataColumn();
            $row_range    = range(2, $row_limit);
            $column_range = range('D', $column_limit);
            $startcount = 2;
            $data = array();
            foreach ($row_range as $row) {
                $data[] = [
                    'name' => $sheet->getCell('A' . $row)->getValue(),
                    'email' => $sheet->getCell('B' . $row)->getValue(),
                    'contact' => $sheet->getCell('C' . $row)->getValue(),
                    'address' => $sheet->getCell('D' . $row)->getValue(),
                ];
                $startcount++;
            }
            foreach ($data as $key => $value) {
                // return $value;
                $create = Vendor::create([
                    'name' => $value['name'],
                    'email' => $value['email'],
                    'contact_no' => $value['contact'],
                    'address' => $value['address'],
                ]);
            }
        } catch (Exception $e) {
            return back()->withErrors('There was a problem uploading the data!');
        }
        return back()->withSuccess('Great! Data has been successfully uploaded.');
    }
}
