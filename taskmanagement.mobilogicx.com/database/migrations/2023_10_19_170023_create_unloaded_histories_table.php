<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnloadedHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_check_out_consignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('consignment_id')->constrained('consignements')->cascadeOnDelete();

            $table->foreignId('source_type_id')->constrained('inventory_types')->comment('Type of location (e.g., warehouse, depot, site)');
            $table->string('source_id')->comment('ID corresponding to the specific  location');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trip_check_out_consignments');
    }
}
