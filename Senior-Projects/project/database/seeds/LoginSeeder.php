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
			        'surname' => 'Vanden Eynden',
			        'first_name' => 'Siebe',
			        'email' => 'siebe.vandeneynden@student.kdg.be',
			        'student_id' => '0109830-24',
			        'password' => '$2y$10$6hrOzpxNpJ8MFXjFtQszHuR6J005AHIoJ5wKCkNnwLYAmpp2KePj2',
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
