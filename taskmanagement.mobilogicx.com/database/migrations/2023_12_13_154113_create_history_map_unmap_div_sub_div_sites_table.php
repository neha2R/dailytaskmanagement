<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryMapUnmapDivSubDivSitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history_map_unmap_div_sub_div_sites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('source_type_id')->constrained('project_management_types')->cascadeOnUpdate()->comment('Type of project management (e.g., division, subdivision, site)');
            $table->uuid('source_id')->comment('ID corresponding to the specific management type');
            $table->dateTime('date');
            $table->string('action',14);
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
        Schema::dropIfExists('history_map_unmap_div_sub_div_sites');
    }
}
