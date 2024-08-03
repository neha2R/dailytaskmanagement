<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectInputsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_inputs', function (Blueprint $table) {
            $table->id();
            // Foreign Keys
            $table->foreignId('projects_jobs_id')->constrained('project_jobs')->cascadeOnDelete();
            $table->foreignId('input_id')->constrained('inputs');

            $table->decimal('value')->default(0.00);
            $table->string('uom', 50)->nullable();
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
        Schema::dropIfExists('project_inputs');
    }
}
