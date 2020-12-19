<?php

namespace App\Jobs;

use App\Models\Choice;
use App\Models\Image;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class DeleteChoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $deleteAbleChoiceIds;
    public $deleteAbleChoiceImages;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $deleteAbleChoiceIds, array $deleteAbleChoiceImages)
    {
        $this->deleteAbleChoiceIds = $deleteAbleChoiceIds;
        $this->deleteAbleChoiceImages = $deleteAbleChoiceImages;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Delete Choices
        if (count($this->deleteAbleChoiceIds) > 0) {
            Choice::whereIn('id', $this->deleteAbleChoiceIds)->delete();
        }

        // Delete Choices Images
        if (count($this->deleteAbleChoiceImages)) {
            foreach ($this->deleteAbleChoiceImages as $imagePath) {
                Storage::disk(Choice::$disk)->delete($imagePath);
            }

            Image::whereIn('path', $this->deleteAbleChoiceImages)->delete();
        }
    }
}
