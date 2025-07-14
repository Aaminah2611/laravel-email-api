<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function listUsers(Request $request)
    {
        $perPage = $request->query('per_page', 10); // default to 10 per page
        $users = User::orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $users->items(),
            'current_page' => $users->currentPage(),
            'per_page' => $users->perPage(),
            'total' => $users->total(),
            'last_page' => $users->lastPage(),
        ]);
    }
}
