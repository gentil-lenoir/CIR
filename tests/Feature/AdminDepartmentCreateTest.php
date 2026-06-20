<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDepartmentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_municipal_manager_can_open_department_create_page(): void
    {
        $admin = User::query()->create([
            'name' => 'Municipal Manager',
            'email' => 'department-create-page@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MUNICIPAL_MANAGER,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.departments.create'))
            ->assertOk()
            ->assertSee('Create New Department')
            ->assertSee('Create Department');
    }

    public function test_municipal_manager_can_create_department_from_form(): void
    {
        $admin = User::query()->create([
            'name' => 'Municipal Manager',
            'email' => 'department-create-submit@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MUNICIPAL_MANAGER,
        ]);

        $this->actingAs($admin)
            ->post(route('admin.departments.store'), [
                'name' => 'Public Works',
                'description' => 'Handles roads, drainage, and civic infrastructure requests.',
            ])
            ->assertRedirect(route('admin.departments.index'))
            ->assertSessionHas('success', 'Department created successfully.');

        $this->assertDatabaseHas('departments', [
            'name' => 'Public Works',
            'description' => 'Handles roads, drainage, and civic infrastructure requests.',
        ]);
    }
}
