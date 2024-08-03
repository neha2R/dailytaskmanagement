<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehicleServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicle_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->cascadeOnDelete();
            $table->date('serviceDate');
            $table->integer('odometerReading')->nullable();
            $table->integer('kmRun')->nullable();
            $table->integer('timeGap')->nullable();
            $table->string('serviceType')->nullable();
            $table->decimal('serviceAmount', 8, 2)->nullable();
            $table->string('oilChange');
            $table->decimal('oilChangeAmount', 8, 2);
            $table->string('sparePartsChange')->nullable();
            $table->decimal('totalAmount', 8, 2);
            $table->string('document')->nullable();
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
        Schema::dropIfExists('vehicle_services');
    }
}
