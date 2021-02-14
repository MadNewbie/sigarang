<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProvincesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(App\Models\Sigarang\Area\Province::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name')
                ->nullable();
            
            $table->timestamps();
            $table->unsignedBigInteger('created_by')
                ->nullable();
            $table->unsignedBigInteger('updated_by')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(App\Models\Sigarang\Area\Province::getTableName());
    }
}
