<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');
        $today = date('Y-m-d');
        $in3 = date('Y-m-d', strtotime('+3 days'));

        $notes = DB::table('notes')
            ->where('user_id', $uid)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $todayTasks = DB::table('tasks')
            ->where('user_id', $uid)
            ->where('due_date', $today)
            ->get();

        $urgentTasks = DB::table('tasks')
            ->where('user_id', $uid)
            ->where('status', 'pending')
            ->whereBetween('due_date', [$today, $in3])
            ->orderBy('due_date')
            ->get();

        $sc = DB::table('study_sessions')
            ->selectRaw('COALESCE(SUM(duration_seconds),0) as s')
            ->where('user_id', $uid)
            ->whereRaw('DATE(started_at) = ?', [$today])
            ->first();

        $minutesToday = (int)round(((int)$sc->s) / 60);

        return view('dashboard.index', compact('notes', 'todayTasks', 'urgentTasks', 'minutesToday'));
    }
}
