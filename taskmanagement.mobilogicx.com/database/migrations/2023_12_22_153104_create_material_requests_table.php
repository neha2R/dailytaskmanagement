<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMaterialRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            
            $table->uuid('site_id')->index()->comment('The UUID of the associated sites');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->foreignId('user_id')->constrained('users');
            $table->dateTime('date')->nullable();
            $table->enum('status', ['pending', 'ready_to_pickup', 'out_for_delivery', 'delivered'])->default('pending');
            $table->unsignedInteger('products_count')->default(0);
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
        Schema::dropIfExists('material_requests');
    }
}
