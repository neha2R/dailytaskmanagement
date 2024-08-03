<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function($table) {
            $table->bigInteger('mobile');
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('is_active')->nullable()->default(true);
            $table->foreignId('role_id')->nullable()->constrained('roles')->comment="role id = role's table uid(unique id)";
            $table->foreignId('department_id')->nullable()->constrained('departments')->cascadeOnDelete()->comment="department id = department's table uid(unique id)";
            $table->foreignId('position_id')->nullable()->constrained('positions')->cascadeOnDelete();
            $table->integer('senior_id')->comment('Senoir id ')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function($table) {
            $table->dropColumn('mobile');
            $table->dropColumn('profile_photo_path');
            $table->dropColumn('is_active');
            $table->dropForeign('users_role_id_foreign');
            $table->dropColumn('role_id');
            $table->dropForeign('users_department_id_foreign');
            $table->dropColumn('department_id');
            $table->dropForeign('users_position_id_foreign');
            $table->dropColumn('position_id');

            $table->dropColumn('senior_id');
        });
    }
}
