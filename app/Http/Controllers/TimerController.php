<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TimerController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');
        $today = date('Y-m-d');

        $todaySessions = DB::table('study_sessions')
            ->where('user_id', $uid)
            ->whereRaw('DATE(started_at)=?', [$today])
            ->orderByDesc('started_at')
            ->limit(10)
            ->get();

        $sum = DB::table('study_sessions')
            ->selectRaw('COALESCE(SUM(duration_seconds),0) as s')
            ->where('user_id', $uid)
            ->whereRaw('DATE(started_at)=?', [$today])
            ->first();

        $minutesToday = (int)round(((int)$sum->s) / 60);

        return view('timer.index', compact('todaySessions', 'minutesToday'));
    }

    public function log(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');

        $data = $request->validate([
            'duration_seconds' => 'required|integer|min:1|max:86400',
            'started_at'       => 'required|date',
            'ended_at'         => 'required|date',
        ]);

        DB::table('study_sessions')->insert([
            'user_id' => $uid,
            'course_id' => null,
            'duration_seconds' => $data['duration_seconds'],
            'started_at' => $data['started_at'],
            'ended_at' => $data['ended_at'],
        ]);

        return response()->json(['ok' => true]);
    }
}
