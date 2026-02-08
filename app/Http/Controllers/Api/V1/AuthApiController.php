<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:100',
            'class' => 'required|string|max:50',
        ]);

        $user = User::where('name', $data['name'])
            ->where('class', $data['class'])
            ->first();

        if (!$user) {
            $user = User::create([
                'name'  => $data['name'],
                'class' => $data['class'],
            ]);
        }

        // Optional: hapus token lama biar 1 device 1 token
        // $user->tokens()->delete();

        $token = $user->createToken('android')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id' => $user->id,
                'name' => $user->name,
                'class' => $user->class,
            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();
        return response()->json(['ok' => true]);
    }
}
