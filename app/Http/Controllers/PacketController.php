<?php

namespace App\Http\Controllers;

use App\Helpers\Transformer;
use App\Http\Requests\PacketRequest;
use App\Http\Resources\PacketsCollection;
use App\Models\Packet;
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
                
            return Transformer::ok('Success to create packet.', $packet, 201);
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
            $packet->delete();

            return Transformer::ok('Success to delete packet.', $packet);
        } catch (\Throwable $th) {
            return Transformer::fail('Failed to delete packet.', [
                'error' => $th
            ]);
        }
    }
}
