<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripConsignementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_consignements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('consignment_id')->constrained('consignements')->cascadeOnDelete();

            $table->foreignId('origin_source_type_id')->constrained('inventory_types')->comment('Type of origin location (e.g., warehouse, depot, site)');
            $table->string('origin_source_id')->comment('ID corresponding to the specific origin location');
           
            $table->foreignId('destination_source_type_id')->constrained('inventory_types')->comment('Type of delivery location (e.g., warehouse, depot, site)');
            $table->string('destination_source_id')->comment('ID corresponding to the specific origin location');
            
            $table->enum('status',['pending','trip_assigned','delivered'])->default('pending');

            $table->dateTime('delivery_date')->nullable();
            $table->dateTime('deliver_at')->nullable();
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
        Schema::dropIfExists('trip_consignements');
    }
}
