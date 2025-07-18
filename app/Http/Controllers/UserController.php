<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


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

    public function trashed()
{
    $trashed = User::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);

    return response()->json([
        'data' => $trashed->items(),
        'current_page' => $trashed->currentPage(),
        'per_page' => $trashed->perPage(),
        'total' => $trashed->total(),
        'last_page' => $trashed->lastPage(),
    ]);
}

public function restore($id)
{
    $user = User::onlyTrashed()->findOrFail($id);
    $user->restore();

    return response()->json([
        'message' => 'User restored successfully.',
        'user' => $user
    ], Response::HTTP_OK);
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();  // soft delete, requires `use SoftDeletes` in User model
    return response()->json(['message' => 'User soft-deleted successfully.']);
}

}
