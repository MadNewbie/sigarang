<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMarketPointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(App\Models\Sigarang\Area\MarketPoint::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('market_id')
                ->nullable();
            $table->point('area')
                ->nullable();
            
            $table->timestamps();
            
            $table->unsignedBigInteger('created_by')
                ->nullable();
            $table->unsignedBigInteger('updated_by')
                ->nullable();
            
            $table->foreign('market_id')
                ->references('id')
                ->on(\App\Models\Sigarang\Area\Market::getTableName())
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
        Schema::dropIfExists(App\Models\Sigarang\Area\MarketPoint::getTableName());
    }
}
