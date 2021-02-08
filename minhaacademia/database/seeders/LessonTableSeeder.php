<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lesson;

class LessonTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Lesson::create([
            'title' => 'Teste Aula 1',
            'video' => null,
            'description' => 'Teste 1 de Aula.',
            'order' => '0',
            'module' => '1',
        ]);

        Lesson::create([
            'title' => 'Teste Aula 2',
            'video' => null,
            'description' => 'Teste 2 de Aula.',
            'order' => '1',
            'module' => '1',
        ]);

        Lesson::create([
            'title' => 'Teste Aula 3',
            'video' => null,
            'description' => 'Teste 3 de Aula.',
            'order' => '0',
            'module' => '2',
        ]);

        Lesson::create([
            'title' => 'Teste Aula 1',
            'video' => null,
            'description' => 'Teste 1 de Aula.',
            'order' => '0',
            'module' => '3',
        ]);

        Lesson::create([
            'title' => 'Teste Aula 2',
            'video' => null,
            'description' => 'Teste 2 de Aula.',
            'order' => '1',
            'module' => '3',
        ]);
    }
}
