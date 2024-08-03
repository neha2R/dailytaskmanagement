<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToCheckoutProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('checkout_products', function (Blueprint $table) {
            $table->boolean('is_extra_missing')->default(false)->after('missing_damage_quantity');
            $table->text('description')->nullable()->after('is_extra_missing');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('checkout_products', function (Blueprint $table) {
            $table->dropColumn('is_extra_missing');
            $table->dropColumn('description');
        });
    }
}
