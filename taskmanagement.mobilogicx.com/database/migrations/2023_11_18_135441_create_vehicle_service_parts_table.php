<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleServicePartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_service_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('vehicle_services')->cascadeOnDelete();
            $table->string('sparePartsName');
            $table->decimal('sparePartsAmount', 8, 2);
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
        Schema::dropIfExists('vehicle_service_parts');
    }
}
