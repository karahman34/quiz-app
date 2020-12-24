<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Resources\SessionResource;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionController extends Controller
{
    /**
     * Session not found json response.
     *
     * @return  JsonResponse
     */
    private function notFoundResponse()
    {
        return Transformer::modelNotFound('Session');
    }

    /**
     * Search packet.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function searchPacket(Request $request)
    {
        $request->validate([
            'code' => 'required|string'
        ]);

        try {
            $session = Session::with(['packet' => function ($query) {
                $query->with('author')->withCount('quizzes');
            }])
            ->where('code', $request->get('code'))
            ->where('status', 'on_going')
            ->firstOrFail();

            return Transformer::ok('Success to get packet.', $session);
        } catch (ModelNotFoundException $th) {
            return $this->notFoundResponse();
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to get packet.', ['error' => $th->getMessage()]);
        }
    }

    /**
     * Check if the user is already finish
     * the session.
     *
     * @param   Session  $session
     *
     * @return  bool
     */
    private function userFinishedSession(Session $session)
    {
        return $session->participants()
                            ->wherePivot('user_id', Auth::id())
                            ->wherePivot('status', 'finished')
                            ->exists();
    }

    /**
     * Join Session.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function join(Request $request)
    {
        if (strtolower($request->method()) !== 'post') {
            if ($request->session()->get('session.working') === true) {
                return redirect()->route('session.start', [
                    'session' => $request->session()->get('session.code')
                ]);
            }

            return view('join-session');
        }

        $request->validate([
            'code' => 'required|string'
        ]);

        try {
            // Get session
            $session = Session::where('code', $request->get('code'))
                                ->where('status', 'on_going')
                                ->firstOrFail();

            // Validate user
            if ($this->userFinishedSession($session)) {
                return Transformer::fail('You already finish this session.', null, 403);
            }
            
            // Join user
            $now = Carbon::now();
            $auth = Auth::user();
            $joined = $session->participants()->wherePivot('user_id', Auth::id())->exists();
            if (!$joined) {
                $session->participants()->attach($auth->id, [
                    'status' => 'on_going',
                    'joined_at' => $now,
                ]);

                $request->session()->put('session.working', true);
                $request->session()->put('session.code', $request->get('code'));
            }

            return Transformer::ok('Success to join the session.');
        } catch (ModelNotFoundException $th) {
            return $this->notFoundResponse();
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to join session.', ['error' => $th->getMessage()]);
        }
    }

    /**
     * Start working on the session.
     *
     * @param   Session  $session
     *
     * @return  mixed
     */
    public function start(Session $session)
    {
        if (session('session.working') !== true) {
            return abort(403);
        }

        // Load packet,quizzes and the chocies
        $session->load(['packet', 'packet.quizzes', 'packet.quizzes.image', 'packet.quizzes.choices' => function ($query) {
            $query->select('id', 'quiz_id', 'text', 'created_at', 'updated_at')
                    ->with('image');
        }]);

        return view('start', [
            'session' => new SessionResource($session),
        ]);
    }

    /**
     * Calculate user score.
     *
     * @param   Session  $session
     * @param   array    $quizzes
     * @param   array    $answers
     *
     * @return  number
     */
    private function calculateScore(Session $session, array $quizzes, array $answers)
    {
        // Load quizzes & right choice
        $session->load([
            'packet',
            'packet.quizzes',
            'packet.quizzes.choices' => function ($query) {
                $query->where('is_right', 'Y');
            },
        ]);

        $sessionQuizzes = $session->packet->quizzes;

        $right= 0;
        foreach ($answers as $index => $answer) {
            $quiz_id = $quizzes[$index];
            $sessionQuiz = $sessionQuizzes->firstWhere('id', $quiz_id);

            if ((int) $answer === $sessionQuiz->choices[0]->id) {
                $right += 1;
            }
        }

        return $right / $sessionQuizzes->count() * 100;
    }

    /**
     * Finish Session.
     *
     * @param   Request  $request
     * @param   Session  $session
     *
     * @return  JsonResponse
     */
    public function finishSession(Request $request, Session $session)
    {
        $request->validate([
            'quizzes' => 'required|array',
            'quizzes.*' => 'integer',
            'answers' => 'required|array',
            'answers.*' => 'nullable|integer',
        ]);

        try {
            // Check submit time
            $now = Carbon::now();
            $explodedAvailableFor = explode(':', $session->available_for);
            $session_end_at = Carbon::parse($session->created_at)->addHours($explodedAvailableFor[0])->addMinutes($explodedAvailableFor[1]);
            if ($now > $session_end_at) {
                return Transformer::fail('The session is already over.', null, 403);
            }

            // Validate user
            $userExists = $session->participants()
                                    ->wherePivot('user_id', Auth::id())
                                    ->wherePivot('status', 'on_going')
                                    ->exists();
            if (!$userExists) {
                return Transformer::fail('You are not joining this session.', null, 403);
            }

            // Finish the user
            $session->participants()->wherePivot('user_id', Auth::id())->updateExistingPivot(Auth::id(), [
                'status' => 'finished',
                'score' => $this->calculateScore($session, $request->get('quizzes'), $request->get('answers')),
                'finished_at' => $now,
            ]);

            $request->session()->forget('session.working');
            $request->session()->forget('session.code');
            $request->session()->flash('session.success', true);

            return Transformer::ok('Success to store user score.', $session->participants()->wherePivot('user_id', Auth::id())->first());
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to store user score data.', [
                'error' => $th->getMessage(),
            ]);
        }
    }
}
