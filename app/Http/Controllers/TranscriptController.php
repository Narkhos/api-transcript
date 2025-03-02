<?php

namespace App\Http\Controllers;

use App\Models\Transcript;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TranscriptController extends Controller
{
    public function index(Request $request): ?JsonResponse
    {
        $query = Transcript::select('*')
            ->orderBy('game_uuid')
            ->orderBy('turn');
        if (!is_null($request->header('game_uuid'))) {
            // filtrer sur une partie en partulier
            $query = $query->where('game_uuid', $request->header('game_uuid'));
        }

        $result = $query->get();

        return response()->json($result);
    }

    public function games(Request $request)
    {
        $results = Transcript::selectRaw('game_uuid, MAX(created_at) as latest_created_at')
            ->groupBy('game_uuid')
            ->orderBy('latest_created_at', 'desc')
            ->get();

        return response()->json($results);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate(
            [
                'game_uuid' => 'uuid|required',
                'turn' => 'integer|required',
                'text' => 'string|nullable',
            ]
        );

        $transcript = Transcript::create($request->all());

        return response()->json($transcript);
    }
}
