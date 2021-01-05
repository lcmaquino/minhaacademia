<?php

use Illuminate\Database\Seeder;
use App\Module;
class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Module::create([
            'title' => 'Módulo 1' ,
            'course' => '1',
            'order' => '0',
        ]);

        Module::create([
            'title' => 'Módulo 2' ,
            'course' => '1',
            'order' => '1',
        ]);

        Module::create([
            'title' => 'Módulo 1' ,
            'course' => '2',
            'order' => '0',
        ]);
    }
}
