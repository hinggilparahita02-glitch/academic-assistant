<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotesController extends Controller
{
    public function index(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');
        $notes = DB::table('notes')
            ->where('user_id', $uid)
            ->orderByDesc('pinned')
            ->orderByDesc('created_at')
            ->get();
       
        
        return view('notes.index', compact('notes'));
    }

    public function store(Request $request)
    {
        $uid = (int)$request->session()->get('user_id');

        $data = $request->validate([
            'title'   => 'required|string|max:150',
            'content' => 'required|string',
            'tag'     => 'nullable|string|max:30',
            'pinned'  => 'nullable',
        ]);

        DB::table('notes')->insert([
            'user_id'  => $uid,
            'title'    => $data['title'],
            'content'  => $data['content'],
            'tag'      => $data['tag'] ?? null,
            'pinned'   => $request->has('pinned') ? 1 : 0,
        ]);

        return redirect()->route('notes');
    }

    public function togglePin(Request $request, $id)
    {
        $uid = (int)$request->session()->get('user_id');
        $id = (int)$id;

        $note = DB::table('notes')->where('id', $id)->where('user_id', $uid)->first();
        if (!$note) return redirect()->route('notes');

        DB::table('notes')
            ->where('id', $id)
            ->where('user_id', $uid)
            ->update(['pinned' => $note->pinned ? 0 : 1]);

        return redirect()->route('notes');
    }

    public function destroy(Request $request, $id)
    {
        $uid = (int)$request->session()->get('user_id');
        $id = (int)$id;

        DB::table('notes')->where('id', $id)->where('user_id', $uid)->delete();
        return redirect()->route('notes');
    }
}
