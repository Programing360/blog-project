<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * The roles an admin may assign.
     *
     * @var list<string>
     */
    private const ROLES = ['user', 'author', 'admin'];

    /**
     * List every user with their activity counts.
     */
    public function index(): View
    {
        $users = User::withCount(['posts', 'comments'])
            ->latest()
            ->paginate(10);

        return view('admin.users.index', [
            'users' => $users,
            'roles' => self::ROLES,
        ]);
    }

    /**
     * Show the form for changing a user's role.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', [
            'user' => $user,
            'roles' => self::ROLES,
        ]);
    }

    /**
     * Update a user's role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required', 'string', Rule::in(self::ROLES)],
        ]);

        // An admin demoting themselves would lock themselves out of /admin.
        if ($user->is($request->user()) && $validated['role'] !== 'admin') {
            return back()->with('error', 'You cannot change your own role.');
        }

        $user->update(['role' => $validated['role']]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'Role updated successfully.');
    }

    /**
     * Delete a user. Their posts and comments cascade with them.
     */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return back()->with('error', 'You cannot delete the last remaining admin.');
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User deleted successfully.');
    }
}
