<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsignementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignements', function (Blueprint $table) {
            $table->id()->startingValue(1000);
            $table->foreignId('origin_source_type_id')->constrained('inventory_types')->comment('Type of origin location (e.g., warehouse, depot, site)');
            $table->string('origin_source_id')->comment('ID corresponding to the specific origin location');
           
            $table->foreignId('destination_source_type_id')->constrained('inventory_types')->comment('Type of delivery location (e.g., warehouse, depot, site)');
            $table->string('destination_source_id')->comment('ID corresponding to the specific origin location');
            
            $table->enum('status',['pending','trip_assigned','delivered'])->default('pending');
            $table->dateTime('delivery_by_date')->nullable();

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
        Schema::dropIfExists('consignements');
    }
}
