<?php

use App\Models\Article;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();
// change 'SET FOREIGN_KEY_CHECKS=0 or 1;' => 'TRUNCATE TABLE Comments RESTART IDENTITY CASCADE'
        DB::statement('TRUNCATE TABLE Comments RESTART IDENTITY CASCADE');
        if (app()->environment() != 'production') {
            foreach (Article::all() as $article) {
                Comment::factory()->count(3)->create([
                    'article_id' => $article->id,
                    'user_id' => $faker->randomElement(\App\Models\User::all()->pluck('id')->toArray()),
                ]);
            }
        }
        DB::statement('TRUNCATE TABLE Comments RESTART IDENTITY CASCADE');
    }
}
