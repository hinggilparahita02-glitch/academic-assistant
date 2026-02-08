<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudySessionsApiController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->user()->id;
        $date = $request->query('date', date('Y-m-d'));

        $sessions = DB::table('study_sessions')
            ->where('user_id', $uid)
            ->whereRaw('DATE(started_at)=?', [$date])
            ->orderByDesc('started_at')
            ->get();

        $seconds = (int)DB::table('study_sessions')
            ->where('user_id', $uid)
            ->whereRaw('DATE(started_at)=?', [$date])
            ->sum('duration_seconds');

        return response()->json([
            'date' => $date,
            'total_seconds' => $seconds,
            'total_minutes' => (int)round($seconds / 60),
            'data' => $sessions,
        ]);
    }

    public function store(Request $request)
    {
        $uid = (int)$request->user()->id;

        $data = $request->validate([
            'duration_seconds' => 'required|integer|min:1|max:86400',
            'started_at' => 'required|date',
            'ended_at'   => 'required|date',
        ]);

        $id = DB::table('study_sessions')->insertGetId([
            'user_id' => $uid,
            'course_id' => null,
            'duration_seconds' => $data['duration_seconds'],
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
        ]);

        $ss = DB::table('study_sessions')->where('id', $id)->first();
        return response()->json(['data' => $ss], 201);
    }
}
