<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Issue;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminAccountController extends Controller
{
    public function editProfile(Request $request): View
    {
        /** @var User $admin */
        $admin = $request->user();

        return view('admin.profile', [
            'admin' => $admin,
            'stats' => $this->accountStats(),
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($admin->id),
                Rule::unique('workers', 'email'),
            ],
            'phone' => ['nullable', 'digits:10'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if (blank($validated['password'] ?? null)) {
            unset($validated['password']);
        }

        $admin->update($validated);

        return redirect()
            ->route('admin.profile.edit')
            ->with('success', 'Profile updated successfully.');
    }

    public function editSettings(Request $request): View
    {
        /** @var User $admin */
        $admin = $request->user();

        return view('admin.settings', [
            'admin' => $admin,
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        /** @var User $admin */
        $admin = $request->user();

        $validated = $request->validate([
            'theme_preference' => ['required', Rule::in(['light', 'dark'])],
        ]);

        $admin->update([
            'theme_preference' => $validated['theme_preference'],
        ]);

        return redirect()
            ->route('admin.settings.edit')
            ->with('success', 'Settings saved successfully.');
    }

    private function accountStats(): array
    {
        return [
            'workers' => Worker::query()->count(),
            'departments' => Department::query()->count(),
            'open_issues' => Issue::query()->where('status', '!=', 'resolved')->count(),
        ];
    }
}
