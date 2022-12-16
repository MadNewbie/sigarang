<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(\App\Models\Sigarang\Area\Market::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name')
                ->nullable();
            $table->unsignedBigInteger('district_id')
                ->nullable();
            
            $table->timestamps();
            $table->unsignedBigInteger('created_by')
                ->nullable();
            $table->unsignedBigInteger('updated_by')
                ->nullable();
            
            $table->foreign('district_id')
                ->references('id')
                ->on(\App\Models\Sigarang\Area\District::getTableName())
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
        Schema::dropIfExists(\App\Models\Sigarang\Area\Market::getTableName());
    }
}
