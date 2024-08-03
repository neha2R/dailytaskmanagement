<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProjectIdToConsignements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('consignements', function (Blueprint $table) {
            Schema::table('consignements', function (Blueprint $table) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->comment('Foreign key for the projects table')->before('created_at');
            });   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('consignements', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
            $table->dropColumn('project_id');
        }); 
    }
}
