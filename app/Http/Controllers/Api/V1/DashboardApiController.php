<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardApiController extends Controller
{
    public function summary(Request $request)
    {
        $uid = (int)$request->user()->id;
        $today = date('Y-m-d');
        $in3   = date('Y-m-d', strtotime('+3 days'));

        $notes = DB::table('notes')
            ->where('user_id', $uid)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $todayTasks = DB::table('tasks')
            ->where('user_id', $uid)
            ->where('due_date', $today)
            ->orderByDesc('created_at')
            ->get();

        $urgentTasks = DB::table('tasks')
            ->where('user_id', $uid)
            ->where('status', 'pending')
            ->whereBetween('due_date', [$today, $in3])
            ->orderBy('due_date')
            ->get();

        $secondsToday = (int)DB::table('study_sessions')
            ->where('user_id', $uid)
            ->whereRaw('DATE(started_at)=?', [$today])
            ->sum('duration_seconds');

        $minutesToday = (int)round($secondsToday / 60);

        return response()->json([
            'minutes_today' => $minutesToday,
            'notes' => $notes,
            'today_tasks' => $todayTasks,
            'urgent_tasks' => $urgentTasks,
        ]);
    }
}
