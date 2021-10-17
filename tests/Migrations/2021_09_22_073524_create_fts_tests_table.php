<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Nin\MySqlFtSearch\Facade as FtSchema;
use Nin\MySqlFtSearch\MySqlFtBlueprint as Blueprint;

class CreateFtsTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        FtSchema::create('fts_tests', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('job');
            $table->timestamps();

            $table->fulltext(['email', 'job']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fts_tests');
    }
}
