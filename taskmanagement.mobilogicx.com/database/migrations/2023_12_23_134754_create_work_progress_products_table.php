<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkProgressProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_progress_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_progress_id')->constrained('work_progress')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('product_masters');
            $table->decimal('quantity')->default(0.00);
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
        Schema::dropIfExists('work_progress_products');
    }
}
