<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('vehicle_body_type')->nullable();
            $table->string('vehicle_condition')->nullable();
            $table->foreignId('manufacturer_id')->constrained('vehicle_manufacturers');
            $table->foreignId('model_id')->constrained('vehicle_models');
            $table->string('vehicle_color')->nullable();
            $table->string('vehicle_number');
            $table->string('chassis_number')->nullable();
            $table->string('engine_number')->nullable();
            $table->integer('wheelbase')->nullable();
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('vehicles');
    }
}
