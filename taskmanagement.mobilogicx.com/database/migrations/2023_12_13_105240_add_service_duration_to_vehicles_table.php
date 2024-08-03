<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddServiceDurationToVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->integer('service_time_duration')->nullable();
            $table->integer('service_km_duration')->nullable();
            $table->dateTime('registration_date')->nullable();
            $table->dateTime('validity_date')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn('service_time_duration');
            $table->dropColumn('service_km_duration');
            $table->dropColumn('registration_date');
            $table->dropColumn('validity_date');
        });
    }
}
