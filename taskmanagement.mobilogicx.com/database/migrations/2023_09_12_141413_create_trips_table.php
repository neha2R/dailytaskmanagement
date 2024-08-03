<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();

            $table->foreignId('origin_source_type_id')->constrained('inventory_types')->comment('Type of origin location (e.g., warehouse, depot, site)');
            $table->string('origin_source_id')->comment('ID corresponding to the specific origin location');
           
            $table->foreignId('destination_source_type_id')->constrained('inventory_types')->comment('Type of delivery location (e.g., warehouse, depot, site)');
            $table->string('destination_source_id')->comment('ID corresponding to the specific origin location');
                       
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('ended_at')->nullable();
            $table->string('delivery_type')->nullable();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('driver_id')->constrained('users');
            $table->enum('status',['pending','ongoing','completed'])->default('pending');

            $table->softDeletes();
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
        // Drop the 'trips' table if it exists
        Schema::dropIfExists('trips');
    }
}
