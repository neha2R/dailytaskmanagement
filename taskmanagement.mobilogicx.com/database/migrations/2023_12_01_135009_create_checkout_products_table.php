<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCheckoutProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checkout_products', function (Blueprint $table) {
            $table->id();

            // Foreign keys
            $table->foreignId('checkout_consignements_id')->constrained('checkout_consignements')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product_masters');

            // Columns
            $table->decimal('actual_quantity', 10, 2)->comment('Actual Quantity');
            $table->decimal('missing_damage_quantity', 10, 2)->comment('Missing/Damage Quantity');

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
        Schema::dropIfExists('checkout_products');
    }
}
