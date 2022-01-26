<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker\Factory::create();

        for ($i = 1; $i <= 10000; $i++) {
            DB::table('students')->insert([
                'name' => $faker->name(),
                'email' => Str::random(10).'@gmail.com',
                'created_at' => $faker->dateTime('now'),
            ]);

            DB::table('courses')->insert([
                'name' => $faker->city(),
                'created_at' => $faker->dateTime('now'),
            ]);
        }

        for ($i = 1; $i <= 30000; $i++) {
            $enroll_date = $faker->dateTimeBetween('-10 years');

            DB::table('enrollments')->insert([
                'student_id' => $faker->numberBetween(1, 10000),
                'course_id' => $faker->numberBetween(1, 10000),
                'status' => $faker->numberBetween(1, 3),
                'enroll_date' => $enroll_date,
                'complete_date' => $faker->dateTimeBetween($enroll_date),
                'created_at' => $faker->dateTime('now'),
            ]);
        }

    }
}
