<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Resources\ActivitiesCollection;
use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
    /**
     * Get user activities collection.
     *
     * @param   Request  $request
     *
     * @return  JsonResponse
     */
    public function index(Request $request)
    {
        try {
            $activities = Auth::user()->activities()
                                        ->when($request->has('order'), function ($query) use ($request) {
                                            if ($request->get('order') === 'old') {
                                                $query->orderBy('created_at');
                                            } else {
                                                $query->orderByDesc('created_at');
                                            }
                                        })
                                        ->when(!$request->has('order'), function ($query) {
                                            $query->orderByDesc('created_at');
                                        })
                                        ->paginate(10);

            return (new ActivitiesCollection($activities));
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to get user activities.', [
                'error' => $th->getMessage()
            ]);
        }
    }

    /**
     * Delete activity.
     *
     * @param   Activity  $activity
     *
     * @return  JsonResponse
     */
    public function destroy(Activity $activity)
    {
        try {
            $activity->delete();

            return Transformer::ok('Success to delete activity.');
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to delete activity.');
        }
    }
}
