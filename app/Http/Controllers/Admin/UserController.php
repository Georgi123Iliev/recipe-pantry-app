<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_admin' => 'required|boolean',
        ]);

        // Prevent admin from demoting themselves
        if ($user->id === auth()->id() && $validated['is_admin'] === false) {
            return back()->withErrors(['error' => 'Не можеш да премахнеш собствените си администраторски права.']);
        }

        $user->update($validated);

        return back();
    }

    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Не можеш да изтриеш собствения си профил.']);
        }

        $user->delete();

        return back();
    }
}
