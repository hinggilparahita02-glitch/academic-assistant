<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function show()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'class' => 'required|string|max:50',
        ]);

        $user = DB::table('users')
            ->where('name', $data['name'])
            ->where('class', $data['class'])
            ->first();

        if (!$user) {
            $id = DB::table('users')->insertGetId([
                'name' => $data['name'],
                'class' => $data['class'],
            ]);
            $user = DB::table('users')->where('id', $id)->first();
        }

        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);
        $request->session()->put('user_class', $user->class);

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }
}
