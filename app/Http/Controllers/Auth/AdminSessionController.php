<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminSessionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([...$credentials, 'role' => User::ROLE_MUNICIPAL_MANAGER], $request->boolean('remember'))) {
            return response()->json([
                'message' => 'Unable to start an admin session with these credentials.',
            ], 422);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'Admin session created.',
            'redirect' => route('admin.dashboard'),
        ]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
