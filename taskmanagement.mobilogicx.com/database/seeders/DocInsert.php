<?php

namespace Database\Seeders;

use App\Models\TripDocument;
use Illuminate\Database\Seeder;

class DocInsert extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Delete all existing records
        // TripDocument::truncate();
        $documents = [
            // 'Driving License',
            // 'Vehicle RC (Registration Certificate)',
            // 'Vehicle Insurance',
            // 'National Permit',
            // 'State Permit',
            // 'Vehicle PUC (Pollution Under Control) Certificate',
            // 'Physical Documents',
            'E-Way Bill',
            'Delivery Challan',
            // 'Material Invoice',
        ];
        foreach ($documents as $key => $value) {
            TripDocument::create([
                'document_name' => $value,
                'is_active' => true
            ]);
        }
    }
}
