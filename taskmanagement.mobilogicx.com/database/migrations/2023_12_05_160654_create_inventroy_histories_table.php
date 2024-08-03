<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventroyHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventroy_histories', function (Blueprint $table) {
            $table->id();
            // Product associated with this inventory history
            $table->foreignId('product_id')->constrained('product_masters');
            // User responsible for the inventory operation
            $table->foreignId('user_id')->constrained('users');

            $table->foreignId('inventory_type_id')->constrained('inventory_types');
            $table->string('source_id')->comment('Specific inventory type ID');
            // Type and source of import/export (e.g., warehouse, depot)
            $table->foreignId('tr_inventory_type_id')->nullable()->constrained('inventory_types');
            $table->string('tr_source_id')->nullable()->comment('Source ID (e.g., warehouse, depot)');
            // Vendor information (nullable for cases without a vendor)
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->decimal('quantity', 8, 2)->default(0.00);
            $table->string('action')->nullable()->comment('Type of action: trip, remaining, added');
            $table->text('description')->nullable()->comment('Additional details or comments');
            $table->softDeletes();
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
        Schema::dropIfExists('inventroy_histories');
    }
}
