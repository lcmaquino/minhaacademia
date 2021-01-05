<?php

use Illuminate\Database\Seeder;
use App\Course;

class CourseTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Course::create([
            'title' => 'Curso 1',
            'video' => null,
            'description'=> 'Teste de curso público.',
            'duration' => '8',
            'visibility' => '1',
            'teacher' => '1',
        ]);

        Course::create([
            'title' => 'Curso 2',
            'video' => null,
            'description'=> 'Teste de curso privado.',
            'duration' => '4',
            'visibility' => '0',
            'teacher' => '1',
        ]);
    }
}
