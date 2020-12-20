<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Requests\ChoiceRequest;
use App\Http\Resources\ChoiceResource;
use App\Models\Choice;
use App\Models\Packet;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ChoiceController extends Controller
{
    /**
     * Store choice's image.
     *
     * @param   UploadedFile  $image
     *
     * @return  string
     */
    private function storeImage(UploadedFile $image)
    {
        return $image->store(Choice::$folder_image, Choice::$disk);
    }

    /**
     * Delete Choice's image.
     *
     * @param   Choice  $choice
     *
     * @return  void
     */
    private function deleteImage(Choice $choice)
    {
        // Delete from storage.
        Storage::disk(Choice::$disk)->delete($choice->image->path);

        // Delete from DB
        $choice->image->delete();
    }

    /**
     * Create Choice Data.
     *
     * @param   ChoiceRequest  $request
     *
     * @return  JsonResponse
     */
    public function store(ChoiceRequest $request)
    {
        $this->authorize('create', Packet::find($request->get('_packet_id')));

        try {
            $quiz = Quiz::select('id', 'packet_id')
                            ->where('id', $request->get('_quiz_id'))
                            ->where('packet_id', $request->get('_packet_id'))
                            ->first();

            $choice = $quiz->choices()->create([
                'text' => $request->get('text')
            ]);

            if ($request->hasFile('image')) {
                $choice->image()->create([
                    'path' => $this->storeImage($request->file('image')),
                ]);
            }

            return Transformer::ok('Success to create choice.', new ChoiceResource($choice), 201);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to create choice.', ['error' => $th]);
        }
    }
    
    /**
     * Update Choice model.
     *
     * @param   ChoiceRequest  $request
     * @param   Choice         $choice
     *
     * @return  JsonResponse
     */
    public function update(ChoiceRequest $request, Choice $choice)
    {
        $this->authorize('update', Packet::find($request->get('_packet_id')));

        try {
            // Update choice model.
            $choice->update(
                $request->only('text'),
            );

            if (!$request->hasFile('image')) {
                // Old image deleted
                if ($request->has('old_image_deleted') && $request->get('old_image_deleted') === 'y') {
                    $this->deleteImage($choice);
                }
            } else {
                // Delete old image
                if ($choice->image) {
                    $this->deleteImage($choice);
                }

                // Store new image
                $choice->image()->create([
                    'path' => $this->storeImage($request->file('image')),
                ]);
            }

            // Refresh choice model.
            $choice->refresh();

            return Transformer::ok('Success to update choice.', new ChoiceResource($choice));
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to update choice.');
        }
    }
    
    /**
     * Delete choice model.
     *
     * @param   Choice  $choice
     *
     * @return  JsonResponse
     */
    public function destroy(Choice $choice)
    {
        $this->authorize('delete', $choice->quiz->packet);

        try {
            // Delete image
            if ($choice->image) {
                $this->deleteImage($choice);
            }

            // Delete choice model.
            $choice->delete();

            return Transformer::ok('Success to delete choice.');
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to delete choice.');
        }
    }
}
