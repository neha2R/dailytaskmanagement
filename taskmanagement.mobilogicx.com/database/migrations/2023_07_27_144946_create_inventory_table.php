<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('product_masters');
            $table->foreignId('inventory_type_id')->constrained('inventory_types')->comment('Type of inventory (e.g., warehouse, depo, site)');
            $table->unsignedBigInteger('source_id')->comment('ID corresponding to the specific inventory type');
            $table->decimal('quantity',8,2)->default(0.00);
            $table->decimal('available_quantity',8,2)->default(0.00)->comment('The available quantity in free state; used to track the quantity when items are in a free state.');
            $table->string('model_name')->nullable();
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
        Schema::dropIfExists('inventory');
    }
}
