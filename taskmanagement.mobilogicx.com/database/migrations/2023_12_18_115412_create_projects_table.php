<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name', 100);
            $table->string('contract_number', 50)->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->date('started_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->enum('status',['to-do','in-process','completed'])->default('to-do');
            $table->unsignedBigInteger('assigned_to');
            $table->unsignedBigInteger('vendor_id');
            $table->string('poc_name', 100);
            $table->string('contact_number', 20);
            $table->string('email', 100);

            $table->foreign('assigned_to')->references('id')->on('users');
            $table->foreign('vendor_id')->references('id')->on('vendors');
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
        Schema::dropIfExists('projects');
    }
}
