<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Outl1ne\NovaSettings\NovaSettings;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // TODO Remove in next major release
        // Legacy support
        // Convert value column to text if needed as the 'value' column was previously a varchar
        Schema::table(NovaSettings::getSettingsTableName(), function (Blueprint $table) {
            $table->text('value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // No down because previous migration was also modified
    }
};
