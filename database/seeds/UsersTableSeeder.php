<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if (env("APP_ENV") == 'local') {
            $owner = User::create(
                [
                    'name' => 'Admin',
                    'password' => \Hash::make('12345678'),
                    'email' => 'hominh4078@gmail.com'
                ]
            );

            $admin = User::create(
                [
                    'name' => 'Admin',
                    'password' => \Hash::make('admin'),
                    'email' => 'mrbright4078@gmail.com'
                ]
            );

            $author = User::create(
                [
                    'name' => 'Author',
                    'password' => \Hash::make('author'),
                    'email' => 'author@gmail.com'
                ]
            );
            $reader = User::create(
                [
                    'name' => 'Reader',
                    'password' => \Hash::make('reader'),
                    'email' => 'reader@gmail.com'
                ]
            );
            $ownerRole = Role::where('name', 'owner')->first();
            $adminRole = Role::where('name', 'admin')->first();
            $authorRole = Role::where('name', 'author')->first();
            $readerRole = Role::where('name', 'reader')->first();
            $owner->assignRole([$ownerRole]);
            $admin->assignRole([$adminRole]);
            $author->assignRole([$authorRole]);
            $reader->assignRole([$readerRole]);
        } else {
            $owner = User::create(
                [
                    'name' => 'bap-jp',
                    'password' => bcrypt('12345678'),
                    'email' => 'admin_bap@bap.com'
                ]
            );
            $ownerRole = Role::where('name', 'owner')->first();

            $owner->assignRole($ownerRole);
        }
    }
}
