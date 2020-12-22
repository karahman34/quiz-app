<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Choice;
use App\Models\Packet;
use Illuminate\Support\Str;
use App\Helpers\Transformer;
use Illuminate\Http\Request;
use App\Jobs\DeleteChoicesJob;
use App\Jobs\DeleteQuizzesJob;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\PacketRequest;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\PacketResource;
use App\Http\Resources\PacketsCollection;
use App\Jobs\UpdateSessionStatusJob;
use App\Models\Session;
use Carbon\Carbon;

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
     * Get packet data.
     *
     * @param   Packet  $packet
     *
     * @return  JsonResponse
     */
    public function show(Packet $packet)
    {
        $this->authorize('view', $packet);

        $packet->load([
            'author',
            'quizzes' => function ($query) {
                $query->with('image', 'choices', 'choices.image')->withCount('choices');
            }])
        ->loadCount(['quizzes']);

        return view('packet', [
            'packet' => new PacketResource($packet)
        ]);
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
                $request->only('title', 'lasts_for'),
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
                $request->only('title', 'lasts_for')
            );

            $packet->refresh();

            return Transformer::ok('Success to update packet.', new PacketResource($packet));
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

    /**
     * Get Packet's Session
     *
     * @param   Request $request
     * @param   Packet  $packet
     *
     * @return  JsonResponse
     */
    public function getSessions(Request $request, Packet $packet)
    {
        try {
            $sessions = $packet->sessions()
                                ->withCount('participants')
                                ->when($request->has('sort'), function ($query) use ($request) {
                                    $query->orderBy($request->get('sort'), $request->get('order', 'asc'));
                                })
                                ->get();

            return Transformer::ok('Success to get packet sessions.', $sessions);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to get packet sessions', ['error' => $th]);
        }
    }

    /**
     * Create Packet Session.
     *
     * @param   Request  $request
     * @param   Packet   $packet
     *
     * @return  JsonResponse
     */
    public function createSession(Request $request, Packet $packet)
    {
        $this->authorize('create', $packet);

        $request->validate([
            'available_for' => 'required|numeric|digits_between:1,24',
        ]);

        try {
            // Check packet session
            if ($packet->sessions()->where('status', 'on_going')->count() > 0) {
                return Transformer::fail('There is already an ongoing session', null, 400);
            }

            // Validate available for
            if ((int) $request->get('available_for') > (int) explode(':', $packet->lasts_for)[0]) {
                return Transformer::fail('The time cannot exceed the packet lasts for times.', null, 400);
            }

            // Create Session
            $session = $packet->sessions()->create([
                'code' => strtoupper(Str::random(10)),
                'available_for' => $request->get('available_for'),
                'status' => 'on_going'
            ]);

            // Update Session status
            UpdateSessionStatusJob::dispatch($session)
                                    ->delay(Carbon::now()->addHours((int) $session->available_for));

            return Transformer::ok('Success to create session.', $session);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to create session.', [
                'error' => $th
            ]);
        }
    }

    /**
     * Get Session's Participants.
     *
     * @param   Request  $request
     * @param   Packet   $packet
     * @param   Session  $session
     *
     * @return  JsonResponse
     */
    public function getSessionParticipants(Request $request, Packet $packet, Session $session)
    {
        try {
            $participants = $session->participants()
                                        ->when($request->has('sort'), function ($query) use ($request) {
                                            $query->orderBy($request->get('sort'), $request->get('order', 'asc'));
                                        })
                                        ->when(!$request->has('sort'), function ($query) {
                                            $query->orderBy('joined_at');
                                        })
                                        ->get();

            return Transformer::ok('Success to get session\'s participants.', $participants);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to get session\'s participants.', ['error' => $th]);
        }
    }

    /**
     * Delete Packete's Session.
     *
     * @param   Request  $request
     * @param   Packet   $packet
     *
     * @return  JsonResponse
     */
    public function deleteSession(Request $request, Packet $packet)
    {
        $this->authorize('delete', $packet);

        $request->validate([
            '_session_id' => 'required|numeric',
        ]);

        try {
            $session = $packet->sessions()->where('id', $request->get('_session_id'))->firstOrFail();

            $session->delete();

            return Transformer::ok('Success to delete session.');
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to delete session.', [
                'error' => $th
            ]);
        }
    }
}
