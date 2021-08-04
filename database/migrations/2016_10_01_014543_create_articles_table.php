<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->text('heading');
            $table->text('content');
            $table->integer('user_id')->unsigned()->nullable();
            $table->foreign('user_id')
                ->references('id')
                ->on('users');
            $table->dateTime('published_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->integer('is_published')->unsigned()->default(1);
            $table->integer('is_deleted')->unsigned()->default(0);
            $table->integer('hit_count')->unsigned()->default(0);
            $table->string('comment_count')->default(0);
            $table->string('like_count')->default(0);
            $table->integer('vote')->unsigned()->default(0);
            $table->integer('category_id')->unsigned()->nullable();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories');
            $table->text('background_img')->nullable();
            $table->text('podcast')->nullable();
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
        Schema::dropIfExists('articles');
    }
}
