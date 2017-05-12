<?php

use Illuminate\Database\Seeder;

class LoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('users')->insert([
	        0 =>
		        array (
			        'id' => 1,
			        'surname' => 'Exelmans',
			        'first_name' => 'Raf',
			        'email' => 'raf.exelmans@kdg.be',
			        'student_id' => '0000000-01',
			        'password' => '$2y$10$YXjC3cpZeM9inCbgQoQkxukZYir12r2dNUvIzfJ/uKzbZ5GPcuWwu',
			        'is_admin' => 1,
			        'class_group_id' => NULL,
			        'deleted_at' => NULL,
			        'remember_token' => NULL,
			        'created_at' => NULL,
			        'updated_at' => NULL,
		        ),
        ]);
    }
}
