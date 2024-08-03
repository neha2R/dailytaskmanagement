<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnloadTimersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('unload_timers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('source_type_id')->constrained('inventory_types')->comment('Type of location (e.g., warehouse, depot, site)');
            $table->string('source_id')->comment('ID corresponding to the specific  location');
            $table->dateTime('unload_start_at');
            $table->dateTime('unload_end_at')->nullable();
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
        Schema::dropIfExists('unload_timers');
    }
}
