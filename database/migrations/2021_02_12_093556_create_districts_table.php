<?php

use App\Models\Sigarang\Area\City;
use App\Models\Sigarang\Area\District;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDistrictsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(District::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name')
                ->nullable();
            $table->unsignedBigInteger('city_id')
                ->nullable();
            
            $table->timestamps();
            $table->unsignedBigInteger('created_by')
                ->nullable();
            $table->unsignedBigInteger('updated_by')
                ->nullable();
            
            $table->foreign('city_id')
                ->references('id')
                ->on(City::getTableName())
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
        Schema::dropIfExists(District::getTableName());
    }
}
