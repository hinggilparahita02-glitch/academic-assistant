<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');

        $month = (int)$request->query('m', (int)date('m'));
        $year  = (int)$request->query('y', (int)date('Y'));

        if ($month < 1 || $month > 12) $month = (int)date('m');
        if ($year < 2000 || $year > 2100) $year = (int)date('Y');

        $start = sprintf('%04d-%02d-01', $year, $month);
        $end   = date('Y-m-t', strtotime($start));

        $tasks = DB::table('tasks')
            ->where('user_id', $uid)
            ->whereBetween('due_date', [$start, $end])
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->get();

        // group by date for calendar markers
        $taskMap = [];
        foreach ($tasks as $t) {
            $taskMap[$t->due_date][] = $t;
        }

        return view('calendar.index', compact('tasks', 'taskMap', 'month', 'year', 'start', 'end'));
    }

    public function store(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');

        $data = $request->validate([
            'title'    => 'required|string|max:150',
            'due_date' => 'required|date',
        ]);

        DB::table('tasks')->insert([
            'user_id'  => $uid,
            'title'    => $data['title'],
            'due_date' => $data['due_date'],
            'status'   => 'pending',
        ]);

        return back();
    }

    public function toggle(Request $request, $id)
    {
        $uid = (int)$request->session()->get('user_id');
        $id = (int)$id;

        $task = DB::table('tasks')->where('id', $id)->where('user_id', $uid)->first();
        if (!$task) return back();

        $new = $task->status === 'done' ? 'pending' : 'done';

        DB::table('tasks')->where('id', $id)->where('user_id', $uid)->update(['status' => $new]);
        return back();
    }

    public function destroy(Request $request, $id)
    {
        $uid = (int)$request->session()->get('user_id');
        $id = (int)$id;

        DB::table('tasks')->where('id', $id)->where('user_id', $uid)->delete();
        return back();
    }
}
