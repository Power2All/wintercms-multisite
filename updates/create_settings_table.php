<?php namespace Power2all\Multisite\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

/**
 * Class CreateSettingsTable
 * @package Power2all\Multisite\Updates
 */
class CreateSettingsTable extends Migration
{

    public function up()
    {
        Schema::create('power2all_multisite_settings', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->text('domain');
            $table->text('theme');
            $table->boolean('is_protected')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('power2all_multisite_settings');
    }

}
