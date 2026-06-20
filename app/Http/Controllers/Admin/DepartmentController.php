<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDepartmentRequest;
use App\Http\Requests\Admin\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        return view('admin.departments.index', [
            'departments' => Department::query()
                ->withCount('workers')
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.departments.create');
    }

    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Department::query()->create([
            'name' => $validated['name'],
            'code' => $this->uniqueCode($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department created successfully.');
    }

    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $validated = $request->validated();

        $department->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        if ($department->workers()->exists()) {
            throw ValidationException::withMessages([
                'department' => 'Move or remove workers from this department before deleting it.',
            ]);
        }

        $department->delete();

        return redirect()
            ->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }

    private function uniqueCode(string $name): string
    {
        $base = Str::upper(Str::substr(Str::slug($name, ''), 0, 8));
        $code = $base !== '' ? $base : 'DEPT';
        $counter = 1;

        while (Department::query()->where('code', $code)->exists()) {
            $code = Str::substr($base, 0, 5).str_pad((string) $counter, 3, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }
}
