<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name',120);
            $table->string('batch_no')->nullable();
            $table->foreignId('category_id')->nullable()->constrained('categories');
            $table->foreignId('sub_category_id')->nullable()->constrained('categories');
            $table->foreignId('company_id')->constrained('companies');
            $table->string('uom');
            $table->integer('min_stock_warehouse')->default(1);
            $table->integer('min_stock_depo')->default(1);
            $table->decimal('price',8,2)->default(0.00);
            $table->boolean('is_active')->default(1);
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
        Schema::dropIfExists('product_masters');
    }
}
