<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectSubTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_sub_tasks', function (Blueprint $table) {
            $table->id();
            // Foreign Keys
            $table->foreignId('projects_jobs_id')->constrained('project_jobs')->cascadeOnDelete();
            $table->foreignId('sub_task_id')->constrained('sub_tasks');

            $table->enum('status',['to-do','in-process','completed'])->default('to-do');

            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->foreignId('vendor_id')->nullable()->constrained('vendors');
            $table->boolean('sub_contract')->default(false)->comment('check for is vendors subcontracts');
            
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
        Schema::dropIfExists('project_sub_tasks');
    }
}
