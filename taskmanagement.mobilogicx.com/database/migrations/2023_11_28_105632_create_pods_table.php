<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('trips')->onDelete('cascade');
            $table->foreignId('consignement_id')->constrained('consignements')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->foreignId('origin_source_type_id')->constrained('inventory_types')->comment('Type of origin location (e.g., warehouse, depot, site)');
            $table->string('origin_source_id')->comment('ID corresponding to the specific origin location');
           
            $table->foreignId('destination_source_type_id')->constrained('inventory_types')->comment('Type of delivery location (e.g., warehouse, depot, site)');
            $table->string('destination_source_id')->comment('ID corresponding to the specific origin location');
                       
            $table->dateTime('date')->nullable();
            $table->string('document_path')->nullable();
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
        Schema::dropIfExists('pods');
    }
}
