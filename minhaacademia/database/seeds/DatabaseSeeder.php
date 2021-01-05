<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(CourseTableSeeder::class);
        $this->call(ModuleTableSeeder::class);
        $this->call(LessonTableSeeder::class);
        $this->call(ActivityTableSeeder::class);
        $this->call(QuestionTableSeeder::class);
        $this->call(ItemTableSeeder::class);
        $this->call(SettingTableSeeder::class);
    }
}
