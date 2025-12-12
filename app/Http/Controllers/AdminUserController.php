<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    /**
     * LIST: Get all users (Admin only)
     */
    public function index()
    {
        $users = User::select('id', 'name', 'email', 'role', 'created_at')->get();
        return response()->json($users);
    }

    /**
     * UPDATE: Allows an Admin to change the role of another user.
     * Protected by the 'admin.only' middleware defined in the routes file.
     */
    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(['student', 'admin', 'faculty'])],
        ]);

        if ($user->id === auth()->id() && $validated['role'] !== 'admin') {
            return response()->json(['message' => 'Forbidden. You cannot demote your own active account.'], 403);
        }

        $user->update(['role' => $validated['role']]);

        return response()->json([
            'message' => "Role for user {$user->name} updated to {$user->role}.",
            'user' => $user
        ], 200);
    }
}

