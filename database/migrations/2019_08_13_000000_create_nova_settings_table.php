<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use OptimistDigital\NovaSettings\NovaSettings;

class CreateNovaSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Settings table
        Schema::create(NovaSettings::getSettingsTableName(), function (Blueprint $table) {
            $table->string('key')->unique()->primary();
            $table->text('value')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(NovaSettings::getSettingsTableName());
    }
}
