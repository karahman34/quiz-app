<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Quiz;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DeleteQuizzesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteAbleQuizIds;
    public $deleteAbleQuizImages;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $deleteAbleQuizIds, array $deleteAbleQuizImages)
    {
        $this->deleteAbleQuizIds = $deleteAbleQuizIds;
        $this->deleteAbleQuizImages = $deleteAbleQuizImages;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Delete Quizzes
        if (count($this->deleteAbleQuizIds) > 0) {
            Quiz::whereIn('id', $this->deleteAbleQuizIds)->delete();
        }

        // Delete Quizzes Images
        if (count($this->deleteAbleQuizImages)) {
            foreach ($this->deleteAbleQuizImages as $imagePath) {
                Storage::disk(Quiz::$disk)->delete($imagePath);
            }

            Image::whereIn('path', $this->deleteAbleQuizImages)->delete();
        }
    }
}
