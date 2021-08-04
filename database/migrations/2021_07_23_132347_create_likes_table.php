<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('article_id')->unsigned();       
            $table->foreign('article_id')
                ->references('id')
                ->on('articles')
                ->onDelete('cascade');

            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            // $table->nullableMorphs('likeable');
            // $table->dateTime('published_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
