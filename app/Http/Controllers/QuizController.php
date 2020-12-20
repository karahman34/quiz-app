<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Requests\QuizRequest;
use App\Http\Resources\QuizResource;
use App\Jobs\DeleteChoicesJob;
use App\Models\Choice;
use App\Models\Packet;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class QuizController extends Controller
{
    /**
     * Store Quiz's image.
     *
     * @param   UploadedFile  $image
     *
     * @return  string
     */
    private function storeImage(UploadedFile $image)
    {
        return $image->store(Quiz::$folder_image, Quiz::$disk);
    }

    /**
     * Delete Quiz's image.
     *
     * @param   Quiz  $quiz
     *
     * @return  void
     */
    private function deleteImage(Quiz $quiz)
    {
        // Delete image from storage.
        Storage::disk(Quiz::$disk)->delete($quiz->image->path);

        // Delete from DB
        $quiz->image->delete();
    }
    
    /**
     * Create new quiz.
     *
     * @param   QuizRequest  $request
     *
     * @return  JsonResponse
     */
    public function store(QuizRequest $request)
    {
        $this->authorize('create', Packet::find($request->get('_packet_id')));

        try {
            $payload = [
                'text' => $request->get('text'),
                'packet_id' => $request->get('_packet_id'),
            ];

            // Create Quiz.
            $quiz = Quiz::create($payload);

            if ($request->has('image')) {
                $quiz->image()->create([
                    'path' => $this->storeImage($request->file('image')),
                ]);
            }

            return Transformer::ok('Success to create quiz.', new QuizResource($quiz), 201);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to create quiz.', [
                'error' => $th
            ]);
        }
    }

    /**
     * Update Quiz data.
     *
     * @param   QuizRequest  $request
     * @param   Quiz         $quiz
     *
     * @return  JsonResponse
     */
    public function update(QuizRequest $request, Quiz $quiz)
    {
        $this->authorize('update', $quiz->packet);

        try {
            $payload = $request->only('text');

            // Delete old image
            if (!$request->hasFile('image')) {
                if ($request->has('old_image_deleted') && $request->get('old_image_deleted') === 'y') {
                    if ($quiz->image) {
                        $this->deleteImage($quiz);
                    }
                }
            } else {
                // Delete old image
                if ($quiz->image) {
                    $this->deleteImage($quiz);
                }

                // Store new image
                $quiz->image()->create([
                    'path' => $this->storeImage($request->file('image')),
                ]);
            }

            $quiz->update($payload);

            $quiz->refresh();

            return Transformer::ok('Success to update quiz.', new QuizResource($quiz));
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to update quiz.', [
                'error' => $th
            ]);
        }
    }

    /**
     * Set quiz right choice.
     *
     * @param   Request  $request
     * @param   Quiz     $quiz
     *
     * @return  JsonResponse
     */
    public function changeRightChoice(Request $request, Quiz $quiz)
    {
        $request->validate([
            'choice_id' => 'required|regex:/^[0-9]+$/'
        ]);

        try {
            $quiz->choices()->where('is_right', 'Y')->update([
                'is_right' => 'N',
            ]);

            $quiz->choices()->where('id', $request->get('choice_id'))->update([
                'is_right' => 'Y'
            ]);

            return Transformer::ok('Success to change right choice.');
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to change right choice.');
        }
    }

    /**
     * Delete quiz's choices.
     *
     * @param   string|int  $quizId
     *
     * @return  void
     */
    private function deleteQuizChoices($quizId)
    {
        $choices = Choice::with('image')->where('quiz_id', $quizId)->get();

        $deleteAbleChoiceIds = array();
        $deleteAbleChoiceImages = array();
        foreach ($choices as $choice) {
            // Put delete able image
            if ($choice->image) {
                array_push($deleteAbleChoiceImages, $choice->image->path);
            }

            // Delete choice object
            array_push($deleteAbleChoiceIds, $choice->id);
        }

        // Dispatch the job
        DeleteChoicesJob::dispatch($deleteAbleChoiceIds, $deleteAbleChoiceImages);
    }

    /**
     * Delete Quiz data.
     *
     * @param   Quiz  $quiz
     *
     * @return  JsonResponse
     */
    public function destroy(Quiz $quiz)
    {
        $this->authorize('update', $quiz->packet);

        try {
            $this->deleteQuizChoices($quiz->id);

            if ($quiz->image) {
                $this->deleteImage($quiz);
            }

            $quiz->delete();

            return Transformer::ok('Success to delete quiz.');
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to delete quiz.', [
                'error' => $th
            ]);
        }
    }
}
