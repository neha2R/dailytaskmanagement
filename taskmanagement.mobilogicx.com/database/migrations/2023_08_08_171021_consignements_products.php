<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ConsignementsProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consignements_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained('consignements')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product_masters');
            $table->integer('quantity');
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
        Schema::dropIfExists('consignements_products');
        
    }
}
