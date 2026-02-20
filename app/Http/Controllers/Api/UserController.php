<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q') ?? $request->input('search');
        $cacheKey = 'users_search_' . md5((string) $query);

        $users = Cache::remember($cacheKey, 300, function () use ($query) {
            return User::query()
                ->when($query, function ($q) use ($query) {
                    $q->where('name', 'like', "%{$query}%")
                        ->orWhere('email', 'like', "%{$query}%");
                })
                ->limit(20)
                ->get()
                ->map(function ($user) {
                    return [
                        'value' => $user->id,
                        'text' => $user->name,
                        'name' => $user->name,
                        'email' => $user->email,
                    ];
                });
        });

        return response()->json($users);
    }
}
