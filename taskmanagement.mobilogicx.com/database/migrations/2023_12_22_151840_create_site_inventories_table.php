<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSiteInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            $table->uuid('site_id')->index()->comment('The UUID of the associated sites');
            $table->foreign('site_id')->references('id')->on('sites');
            $table->foreignId('product_id')->constrained('product_masters');
            $table->decimal('available_stock')->default(0.00);
            $table->decimal('received_stock')->default(0.00);
            $table->decimal('available_quantity')->default(0.00)->comment('The available quantity in free state; used to track the quantity when items are in a free state.');
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
        Schema::dropIfExists('site_inventories');
    }
}
