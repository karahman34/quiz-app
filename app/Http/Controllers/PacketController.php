<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Requests\PacketRequest;
use App\Http\Resources\PacketResource;
use App\Http\Resources\PacketsCollection;
use App\Jobs\DeleteChoicesJob;
use App\Jobs\DeleteQuizzesJob;
use App\Models\Choice;
use App\Models\Packet;
use App\Models\Quiz;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PacketController extends Controller
{
    /**
     * Get packets collection.
     *
     * @param   Request $request
     *
     * @return  JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $auth = Auth::user();
            $packets = $auth->packets()
                                ->withCount('quizzes')
                                ->when($request->has('search'), function ($query) use ($request) {
                                    $query->where('title', 'like', "%{$request->get('search')}%");
                                })
                                ->orderByDesc('created_at')
                                ->paginate($request->get('limit', 12));

            return (new PacketsCollection($packets));
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to get packets collection.', [
                'error' => $th
            ]);
        }
    }

    /**
     * Create packet data.
     *
     * @param   PacketRequest  $request
     *
     * @return  JsonResponse
     */
    public function store(PacketRequest $request)
    {
        try {
            $packet = Packet::create(array_merge(
                $request->only('title'),
                ['user_id' => Auth::id()]
            ));
                
            return Transformer::ok('Success to create packet.', new PacketResource($packet), 201);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to create packet.', [
                'error' => $th
            ]);
        }
    }

    /**
     * Update Packet.
     *
     * @param   PacketRequest  $request
     * @param   Packet         $packet
     *
     * @return  JsonResponse
     */
    public function update(PacketRequest $request, Packet $packet)
    {
        $this->authorize('update', $packet);

        try {
            $packet->update(
                $request->only('title')
            );

            return Transformer::ok('Success to update packet.', $packet);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to update packet.', [
                'error' => $th
            ]);
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
     * Delete packet's quizzes.
     *
     * @param   string|int  $packetId
     *
     * @return  void
     */
    private function deletePacketQuizzes($packetId)
    {
        $quizzes = Quiz::with('image')->where('packet_id', $packetId)->get();

        $deleteAbleQuizIds = array();
        $deleteAbleQuizImages = array();
        foreach ($quizzes as $quiz) {
            // Put delete able image
            if ($quiz->image) {
                array_push($deleteAbleQuizImages, $quiz->image->path);
            }

            // Delete quiz object
            array_push($deleteAbleQuizIds, $quiz->id);

            // Delete quiz's choices
            $this->deleteQuizChoices($quiz->id);
        }

        // Dispatch the job
        DeleteQuizzesJob::dispatch($deleteAbleQuizIds, $deleteAbleQuizImages);
    }

    /**
     * Delete Packet.
     *
     * @param   Packet         $packet
     *
     * @return  JsonResponse
     */
    public function destroy(Packet $packet)
    {
        $this->authorize('delete', $packet);

        try {
            // Delete packet's quizzes
            $this->deletePacketQuizzes($packet->id);

            $packet->delete();

            return Transformer::ok('Success to delete packet.', $packet);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to delete packet.', [
                'error' => $th
            ]);
        }
    }
}
