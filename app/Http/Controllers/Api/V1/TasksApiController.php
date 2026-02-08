<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TasksApiController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->user()->id;

        $from = $request->query('from');
        $to   = $request->query('to');

        $q = DB::table('tasks')->where('user_id', $uid);

        if ($from && $to) {
            $q->whereBetween('due_date', [$from, $to]);
        }

        $tasks = $q->orderBy('due_date')->orderByDesc('created_at')->get();

        return response()->json(['data' => $tasks]);
    }

    public function store(Request $request)
    {
        $uid = (int)$request->user()->id;

        $data = $request->validate([
            'title'    => 'required|string|max:150',
            'due_date' => 'required|date',
        ]);

        $id = DB::table('tasks')->insertGetId([
            'user_id' => $uid,
            'title' => $data['title'],
            'due_date' => $data['due_date'],
            'status' => 'pending',
        ]);

        $task = DB::table('tasks')->where('id', $id)->first();
        return response()->json(['data' => $task], 201);
    }

    public function toggle(Request $request, $id)
    {
        $uid = (int)$request->user()->id;
        $id = (int)$id;

        $task = DB::table('tasks')->where('id', $id)->where('user_id', $uid)->first();
        if (!$task) return response()->json(['message' => 'Not found'], 404);

        $new = $task->status === 'done' ? 'pending' : 'done';

        DB::table('tasks')->where('id', $id)->where('user_id', $uid)
            ->update(['status' => $new]);

        $updated = DB::table('tasks')->where('id', $id)->first();
        return response()->json(['data' => $updated]);
    }

    public function destroy(Request $request, $id)
    {
        $uid = (int)$request->user()->id;
        $id = (int)$id;

        DB::table('tasks')->where('id', $id)->where('user_id', $uid)->delete();
        return response()->json(['ok' => true]);
    }
}
