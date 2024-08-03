<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutConsignementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout_consignements', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('consignment_id')->constrained('consignements')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            
            $table->foreignId('origin_source_type_id')->constrained('inventory_types')->comment('Type of origin location (e.g., warehouse, depot, site)');
            $table->string('origin_source_id')->comment('ID corresponding to the specific origin location');
           
            $table->foreignId('destination_source_type_id')->constrained('inventory_types')->comment('Type of delivery location (e.g., warehouse, depot, site)');
            $table->string('destination_source_id')->comment('ID corresponding to the specific origin location');
                       

            $table->dateTime('date')->nullable()->comment('Date of checkout');
            $table->boolean('is_checked')->default(false)->comment('Whether the checkout is checked');
            
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
        Schema::dropIfExists('checkout_consignements');
    }
}
