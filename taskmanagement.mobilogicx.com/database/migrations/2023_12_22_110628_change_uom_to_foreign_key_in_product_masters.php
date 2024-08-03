<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeUomToForeignKeyInProductMasters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_masters', function (Blueprint $table) {
            // Drop the existing 'uom' column
            $table->dropColumn('uom');

            // Add the new 'uom_id' foreign key column
            $table->foreignId('uom_id')->nullable()->constrained('uoms'); // Assuming 'uom' is the table name
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_masters', function (Blueprint $table) {
            // Reverse the changes in the 'up' method
            $table->dropForeign(['uom_id']);
            $table->dropColumn('uom_id');

            // Recreate the 'uom' column
            $table->string('uom');
        });
    }
}
