<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueValuesOfYogaClasses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('yoga_classes', function (Blueprint $table) {
            $table->dropUnique(['date']);
            $table->unique(['date', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('yoga_classes', function (Blueprint $table) {
            $table->dropUnique(['date', 'user_id']);
            $table->unique(['date']);
        });
    }
}
