<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Item;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($question = 1; $question < 10; $question++) {
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