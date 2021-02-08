<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Activity::create([
            'title' => 'Atividade 1',
            'description' => 'Teste 1 de Atividade.',
            'order' => '0',
            'module' => '1',
        ]);

        Activity::create([
            'title' => 'Atividade 2',
            'description' => 'Teste 2 de Atividade.',
            'order' => '1',
            'module' => '1',
        ]);

        Activity::create([
            'title' => 'Atividade 3',
            'description' => 'Teste 3 de Atividade.',
            'order' => '2',
            'module' => '1',
        ]);

        Activity::create([
            'title' => 'Atividade 4',
            'description' => 'Teste 4 de Atividade.',
            'order' => '0',
            'module' => '2',
        ]);

        Activity::create([
            'title' => 'Atividade 5',
            'description' => 'Teste 5 de Atividade.',
            'order' => '1',
            'module' => '2',
        ]);
    }
}