<?php

use App\Models\Sigarang\Area\City;
use App\Models\Sigarang\Area\Province;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(City::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string("name")
                ->nullable();
            $table->unsignedBigInteger("province_id")
                ->nullable();
            
            $table->timestamps();
            $table->unsignedBigInteger("created_by")
                ->nullable();
            $table->unsignedBigInteger("updated_by")
                ->nullable();
            
            $table->foreign('province_id')
                ->references('id')
                ->on(Province::getTableName())
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(City::getTableName());
    }
}
