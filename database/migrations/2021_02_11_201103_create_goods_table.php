<?php

use App\Models\Sigarang\Goods\Category;
use App\Models\Sigarang\Goods\Goods;
use App\Models\Sigarang\Goods\Unit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(Goods::getTableName(), function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->string('name')
                ->nullable();
            $table->unsignedBigInteger('unit_id')
                ->nullable();
            $table->unsignedBigInteger('category_id')
                ->nullable();
            
            $table->timestamps();
            $table->unsignedBigInteger('created_by')
                ->nullable();
            $table->unsignedBigInteger('edited_by')
                ->nullable();
            
            $table->foreign('unit_id')
                ->references('id')
                ->on(Unit::getTableName())
                ->onUpdate('cascade')
                ->onDelete('restrict');
            
            $table->foreign('category_id')
                ->references('id')
                ->on(Category::getTableName())
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
        Schema::dropIfExists(Goods::getTableName());
    }
}
