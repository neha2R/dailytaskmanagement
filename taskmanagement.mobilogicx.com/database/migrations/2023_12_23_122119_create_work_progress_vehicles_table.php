<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkProgressVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_progress_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_progress_id')->constrained('work_progress')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->constrained('vehicles')->comment('use only for machinery');
            $table->unsignedInteger('total_duration_minutes')->default(0);
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
        Schema::dropIfExists('work_progress_vehicles');
    }
}
