<?php

use Illuminate\Database\Seeder;
use App\Item;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($question = 1; $question < 12; $question++) {
            for ($item = 1; $item < 5; $item++) {
                Item::create([
                    'content' => 'Alternativa ' . $item,
                    'order' => $item - 1,
                    'question' => $question,
                ]);
            }
        }
    }
}