<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
	    //$this->call(ClassGroupsSeeder::class);
	    //$this->call(UsersTableSeeder::class);
	    //$this->call(SampleChoiceSeeder::class);
	    $this->call(LoginSeeder::class);
    }
}
