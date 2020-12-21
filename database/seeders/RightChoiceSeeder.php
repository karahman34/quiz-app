<?php

namespace Database\Seeders;

use App\Models\Quiz;
use Illuminate\Database\Seeder;

class RightChoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $quizzes = Quiz::with('choices')->get();

        $quizzes->each(function (Quiz $quiz) {
            // Set right choice
            $quiz->choices()->where('id', $quiz->choices->random()->id)->update([
                'is_right' => 'Y'
            ]);
        });
    }
}
