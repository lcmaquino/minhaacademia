<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;

class QuestionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Question::create([
            'content' => 'Questão 1',
            'answer' => '0',
            'order' => '0',
            'activity' => '1',
        ]);

        Question::create([
            'content' => 'Questão 2',
            'answer' => '0',
            'order' => '1',
            'activity' => '1',
        ]);

        Question::create([
            'content' => 'Questão 1',
            'answer' => '0',
            'order' => '0',
            'activity' => '2',
        ]);

        Question::create([
            'content' => 'Questão 2',
            'answer' => '0',
            'order' => '1',
            'activity' => '2',
        ]);

        Question::create([
            'content' => 'Questão 1',
            'answer' => '0',
            'order' => '0',
            'activity' => '3',
        ]);

        Question::create([
            'content' => 'Questão 2',
            'answer' => '0',
            'order' => '1',
            'activity' => '3',
        ]);

        Question::create([
            'content' => 'Questão 1',
            'answer' => '0',
            'order' => '0',
            'activity' => '4',
        ]);

        Question::create([
            'content' => 'Questão 2',
            'answer' => '0',
            'order' => '1',
            'activity' => '4',
        ]);

        Question::create([
            'content' => 'Questão 1',
            'answer' => '0',
            'order' => '0',
            'activity' => '5',
        ]);
    }
}
