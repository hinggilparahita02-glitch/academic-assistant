<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesApiController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->user()->id;

        $notes = DB::table('notes')
            ->where('user_id', $uid)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get();

        return response()->json(['data' => $notes]);
    }

    public function store(Request $request)
    {
        $uid = (int)$request->user()->id;

        $data = $request->validate([
            'title'   => 'required|string|max:150',
            'content' => 'required|string',
            'tag'     => 'nullable|string|max:30',
            'pinned'  => 'nullable|boolean',
        ]);

        $id = DB::table('notes')->insertGetId([
            'user_id' => $uid,
            'title' => $data['title'],
            'content' => $data['content'],
            'tag' => $data['tag'] ?? null,
            'pinned' => !empty($data['pinned']) ? 1 : 0,
        ]);

        $note = DB::table('notes')->where('id', $id)->first();

        return response()->json(['data' => $note], 201);
    }

    public function togglePin(Request $request, $id)
    {
        $uid = (int)$request->user()->id;
        $id = (int)$id;

        $note = DB::table('notes')->where('id', $id)->where('user_id', $uid)->first();
        if (!$note) return response()->json(['message' => 'Not found'], 404);

        DB::table('notes')->where('id', $id)->where('user_id', $uid)
            ->update(['pinned' => $note->pinned ? 0 : 1]);

        $updated = DB::table('notes')->where('id', $id)->first();
        return response()->json(['data' => $updated]);
    }

    public function destroy(Request $request, $id)
    {
        $uid = (int)$request->user()->id;
        $id = (int)$id;

        DB::table('notes')->where('id', $id)->where('user_id', $uid)->delete();
        return response()->json(['ok' => true]);
    }
}
