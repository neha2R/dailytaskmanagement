<?php

namespace App\Http\Controllers;

use App\Models\VehicleDocumentAttribute;
use App\Models\VehicleDocuments;
use App\Models\Vehicles;
use Carbon\Carbon;
use Illuminate\Http\Request;

class VehicleDocumentController extends Controller
{
    public function storeInsuranceDocument(Request $request)
    {
        $request->validate([
            'policy_number' => 'required|min:3',
            'policy_type' => 'required',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'registration_date' => 'required|date',
            'issuer_name' => 'required',
            'document' => 'required', // Adjust the allowed file types as needed
        ], [
            'document.required' => 'Please upload the insurance document.',
        ]);

        $insertData = [
            'document_name' => 'Insurance Certificate',
            'document_type' => 'IC',
            'document_short_name' => 'Insurance',
            'vehicle_id' => $request->input('vehicle_id'),

            'document_number' => $request->input('policy_number'),
            'valid_from' => dateformat($request->input('valid_from'), 'Y-m-d'),
            'valid_to' => dateformat($request->input('valid_to') . ' 23:59:59', 'Y-m-d H:i:s'),
            'registration_date' => dateformat($request->input('registration_date'), 'Y-m-d'),
            'issuer_name' => $request->input('issuer_name'),
        ];

        $additionalAttributes = [
            'Policy Type' => $request->input('policy_type'),
        ];

        $this->storeDocument($request, $insertData, $additionalAttributes);

        // Redirect back with a success message
        return back()->with('success', 'Document saved successfully');
    }
    public function storePUCCDocument(Request $request)
    {
        $request->validate([
            'document_number' => 'required|min:3',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'registration_date' => 'required|date',
            'test_date' => 'required|date',
            'issuer_name' => 'required',
            'document' => 'required', // Adjust the allowed file types as needed
        ], [
            'document.required' => 'Please upload the insurance document.',
        ]);

        $insertData = [
            'document_name' => 'Pollution Under Control Certificate',
            'document_type' => 'PUCC',
            'document_short_name' => 'PUCC',
            'vehicle_id' => $request->input('vehicle_id'),

            'document_number' => $request->input('document_number'),
            'valid_from' => dateformat($request->input('valid_from'), 'Y-m-d'),
            'valid_to' => dateformat($request->input('valid_to') . ' 23:59:59', 'Y-m-d H:i:s'),
            'registration_date' => dateformat($request->input('registration_date'), 'Y-m-d'),
            'issuer_name' => $request->input('issuer_name'),
        ];

        $additionalAttributes = [
            'test_date' => dateformat($request->input('registration_date'), 'Y-m-d'),
        ];

        $this->storeDocument($request, $insertData, $additionalAttributes);

        // Redirect back with a success message
        return back()->with('success', 'Document saved successfully');
    }

    public function storeFitnessDocument(Request $request)
    {
        $request->validate([
            'application_number' => 'required',
            'receipt_number' => 'required',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'issuer_name' => 'required',
            'inspected_on' => 'required|date',
            'document' => 'required', // Adjust the allowed file types as needed
        ], [
            'application_number.required' => 'Please enter the application number.',
            'receipt_number.required' => 'Please enter the receipt number.',
            'valid_from.required' => 'Please select the valid from date.',
            'valid_from.date' => 'Please enter a valid date.',
            'valid_to.required' => 'Please select the valid to date.',
            'valid_to.date' => 'Please enter a valid date.',
            'issuer_name.required' => 'Please enter the issuer name.',
            'inspected_on.required' => 'Please select the inspected date.',
            'inspected_on.date' => 'Please enter a valid date.',
            'document.required' => 'Please upload the Fitness document.',
        ]);

        // Insert data into the database
        $insertData = [
            'document_name' => 'Fitness Certificate',
            'document_type' => 'Fitness',
            'document_short_name' => 'Fitness',
            'vehicle_id' => $request->input('vehicle_id'),

            'document_number' => $request->input('application_number'),
            'valid_from' => Carbon::parse($request->input('valid_from'))->format('Y-m-d'),
            'valid_to' => Carbon::parse($request->input('valid_to'))->endOfDay(), // Set time to 23:59:59
            'issuer_name' => $request->input('issuer_name'),
        ];

        // Insert attributes
        $additionalAttributes = [
            'receipt_number' => $request->input('receipt_number'),
            'inspected_on' => Carbon::parse($request->input('inspected_on'))->format('Y-m-d'),
        ];
        $this->storeDocument($request, $insertData, $additionalAttributes);

        // Redirect back with a success message
        return back()->with('success', 'Document saved successfully');
    }
    public function storeTaxDocument(Request $request)
    {
        $request->validate([
            'document_number' => 'required',
            'registration_date' => 'required|date',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'issuer_name' => 'required',
            'document' => 'required', // Adjust the allowed file types as needed
        ], [
            'document_number.required' => 'Please enter the document number.',
            'registration_date.required' => 'Please enter the registration date.',
            'valid_from.required' => 'Please enter the valid from date.',
            'valid_to.required' => 'Please enter the valid to date.',
            'issuer_name.required' => 'Please enter the issuer name.',
            'document.required' => 'Please upload the Tax document.',
        ]);

        $insertData = [
            'document_name' => 'Tax Certificate',
            'document_type' => 'TC',
            'document_short_name' => 'Tax',
            'vehicle_id' => $request->input('vehicle_id'),

            'document_number' => $request->input('document_number'),
            'valid_from' => Carbon::parse($request->input('valid_from'))->format('Y-m-d'),
            'valid_to' => Carbon::parse($request->input('valid_to'))->endOfDay(), // Set time to 23:59:59
            'registration_date' => Carbon::parse($request->input('registration_date'))->format('Y-m-d'),
            'issuer_name' => $request->input('issuer_name'),
        ];

        $additionalAttributes = [];

        $this->storeDocument($request, $insertData, $additionalAttributes);

        // Redirect back with a success message
        return back()->with('success', 'Document saved successfully');
    }

    public function storeNationalPermitDocument(Request $request)
    {
        $request->validate([
            'document_number' => 'required',
            'permit_category' => 'required',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'issuer_name' => 'required',
            'registration_date' => 'required|date',
            'document' => 'required', // Adjust the allowed file types as needed
        ], [
            'document_number.required' => 'Please enter the document number.',
            'permit_category.required' => 'Please select the permit category.',
            'valid_from.required' => 'Please enter the valid from date.',
            'valid_to.required' => 'Please enter the valid to date.',
            'issuer_name.required' => 'Please enter the issuer name.',
            'registration_date.required' => 'Please enter the registration date.',
            'document.required' => 'Please upload the National Permit document.',
        ]);

        $insertData = [
            'document_name' => 'National Permit Certificate',
            'document_type' => 'NPC',
            'document_short_name' => 'National Permit',
            'vehicle_id' => $request->input('vehicle_id'),

            'document_number' => $request->input('document_number'),
            'valid_from' => Carbon::parse($request->input('valid_from'))->format('Y-m-d'),
            'valid_to' => Carbon::parse($request->input('valid_to'))->endOfDay(), // Set time to 23:59:59
            'issuer_name' => $request->input('issuer_name'),
            'registration_date' => Carbon::parse($request->input('registration_date'))->format('Y-m-d'),
        ];

        $additionalAttributes = [
            'permit_category' => $request->input('permit_category'),
        ];

        $this->storeDocument($request, $insertData, $additionalAttributes);

        // Redirect back with a success message
        return back()->with('success', 'Document saved successfully');
    }

    public function storeStatePermitDocument(Request $request)
    {
        $request->validate([
            'document_number' => 'required',
            'permit_holder_name' => 'required',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
            'issuer_name' => 'required',
            'permit_state' => 'required',
            'registration_date' => 'required|date',
            'document' => 'required', // Adjust the allowed file types as needed
        ], [
            'document_number.required' => 'Please enter the document number.',
            'permit_holder_name.required' => 'Please enter the permit holder name.',
            'valid_from.required' => 'Please enter the valid from date.',
            'valid_to.required' => 'Please enter the valid to date.',
            'issuer_name.required' => 'Please enter the issuer name.',
            'permit_state.required' => 'Please select the state/UT where the permit is valid.',
            'registration_date.required' => 'Please enter the registration date.',
            'document.required' => 'Please upload the State Permit document.',
        ]);

        $insertData = [
            'document_name' => 'State Permit Certificate',
            'document_type' => 'SPC',
            'document_short_name' => 'State Permit',
            'vehicle_id' => $request->input('vehicle_id'),

            'document_number' => $request->input('document_number'),
            'valid_from' => Carbon::parse($request->input('valid_from'))->format('Y-m-d'),
            'valid_to' => Carbon::parse($request->input('valid_to'))->endOfDay(), // Set time to 23:59:59
            'issuer_name' => $request->input('issuer_name'),
            'registration_date' => Carbon::parse($request->input('registration_date'))->format('Y-m-d'),
        ];

        $additionalAttributes = [
            'permit_holder_name' => $request->input('permit_holder_name'),
            'permit_state' => $request->input('permit_state'),
        ];

        $this->storeDocument($request, $insertData, $additionalAttributes);

        // Redirect back with a success message
        return back()->with('success', 'Document saved successfully');
    }

    private function storeDocument(Request $request, array $insertData, array $additionalAttributes)
    {
        $create = VehicleDocuments::create($insertData);

        // Check if a document is present in the request
        if ($request->hasFile('document')) {
            // Save the document to storage
            $path = $request->file('document')->store('vehicleDocuments', 'public');
            // Update the record with the document path
            $create->update(['document_path' => $path]);
            $this->storeAttributes($create, $additionalAttributes);
        }
    }
    private function storeAttributes($document, array $attributes)
    {
        foreach ($attributes as $key => $value) {
            $document->attributes()->create([
                'attribute_name' => $key,
                'attribute_value' => $value,
            ]);
        }
    }

    public function showVehicleDocuments($id)
    {
        try {
            $document = VehicleDocuments::with('attributes')->findOrFail($id);
    
            // Iterate through attributes and check if 'attribute_value' is a date
            $document->attributes->each(function ($attribute) {
                // Check if 'attribute_value' is a date string
                try {
                    $carbonDate = Carbon::parse($attribute->attribute_value);
    
                    // Get the date as a string
                    $formattedDate = $carbonDate->format('d M Y');

                    // Update the attribute value with the formatted date
                    $attribute->attribute_value = $formattedDate;
                } catch (\Exception $e) {
                    // $attribute->attribute_value = null;
                }
            });
    
            return response()->json(['status' => 200, 'document' => $document]);
        } catch (\Exception $e) {
            // Log or handle the exception as needed
            return response()->json(['status' => 500, 'error' => 'Internal Server Error.', "error" => $e->getMessage()], 500);
        }
    }
}
