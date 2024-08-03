<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripCheckOutConsignmentsProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trip_check_out_consignments_products', function (Blueprint $table) {
            $table->id();
            // Foreign keys
            $table->foreignId('trip_checkout_cn_id')->constrained('trip_check_out_consignments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product_masters');

            // Columns
            $table->decimal('actual_quantity', 10, 2)->nullable()->comment('Actual Quantity');
            $table->decimal('missing_damage_quantity', 10, 2)->nullable()->comment('Missing/Damage Quantity');

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
        Schema::dropIfExists('trip_check_out_consignments_products');
    }
}
