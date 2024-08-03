<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_progress', function (Blueprint $table) {
            // Primary key
            $table->id();
            $table->foreignId('project_sub_task_id')->constrained('project_sub_tasks')->comment('Used for a project-specific subtask');
            $table->foreignId('project_id')->nullable()->constrained('projects')->cascadeOnDelete();
            
            $table->uuid('site_id')->index()->comment('The UUID of the associated site');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->foreignId('sub_task_id')->constrained('sub_tasks')->comment('Used to check subtask details');

            $table->foreignId('user_id')->constrained('users');
 

            // Date of the work progress
            $table->dateTime('work_date');

            // Quantity of work progress and associated unit of measure
            $table->decimal('progress_quantity')->default(0.00);
            $table->foreignId('uom_id')->constrained('uoms');

            // Flags for resource utilization
            $table->boolean('labour_used')->default(false);
            $table->decimal('labour_quantity')->default(0.00);
            
            // Quantity of resources used
            $table->boolean('mason_used')->default(false);
            $table->decimal('mason_quantity')->default(0.00);
            
            $table->boolean('machinery_used')->default(false);
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
        Schema::dropIfExists('work_progress');
    }
}
