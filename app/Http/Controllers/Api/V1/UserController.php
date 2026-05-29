<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends BaseApiController
{
    public function index()
    {
        $users = User::select('id', 'first_name', 'last_name', 'email', 'role', 'department_id', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();
        return $this->successResponse($users, 'Users retrieved successfully');
    }

    public function updateRole(Request $request, string $id)
    {
        $validated = $request->validate([
            'role' => 'required|string|in:admin,staff,user,manager',
        ]);

        $user = User::findOrFail($id);

        // Prevent demoting yourself
        if ($user->id === $request->user()->id) {
            return $this->errorResponse('You cannot change your own role.', 403);
        }

        $user->update(['role' => $validated['role']]);

        return $this->successResponse(
            $user->only('id', 'first_name', 'last_name', 'email', 'role'),
            'User role updated successfully'
        );
    }
}
