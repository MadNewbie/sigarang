<?php

use App\Models\Sigarang\Area\Market;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Price;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Price::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->date('date')
                ->nullable();
            $table->integer('price')
                ->nullable();
            $table->unsignedBigInteger('goods_id')
                ->nullable();
            $table->unsignedBigInteger('market_id')
                ->nullable();
            $table->unsignedTinyInteger('type_status')
                ->nullable();
            
            $table->timestamps();
            $table->unsignedBigInteger('created_by')
                ->nullable();
            $table->unsignedBigInteger('updated_by')
                ->nullable();
            
            $table->foreign('goods_id')
                ->references('id')
                ->on(Goods::getTableName())
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->foreign('market_id')
                ->references('id')
                ->on(Market::getTableName())
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
        Schema::dropIfExists(Price::getTableName());
    }
}
