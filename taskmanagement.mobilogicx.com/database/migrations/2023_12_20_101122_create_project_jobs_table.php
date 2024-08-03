<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectJobsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_jobs', function (Blueprint $table) {
            $table->id()->startingValue(10000);
            $table->foreignId('project_id')->constrained('projects');
            $table->foreignId('job_id')->constrained('jobs');

            $table->uuid('division_id')->index()->comment('The UUID of the associated division');
            $table->foreign('division_id')->references('id')->on('divisions');

            $table->uuid('sub_division_id')->index()->comment('The UUID of the associated sub-division');
            $table->foreign('sub_division_id')->references('id')->on('sub_divisions');

            $table->uuid('site_id')->index()->comment('The UUID of the associated sites');
            $table->foreign('site_id')->references('id')->on('sites');

            $table->foreignId('division_head_id')->constrained('users');
            $table->foreignId('site_head_id')->constrained('users');

            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('startrd_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->enum('status',['to-do','in-process','completed'])->default('to-do');

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
        Schema::dropIfExists('project_jobs');
    }
}
