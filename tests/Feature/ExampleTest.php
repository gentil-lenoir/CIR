<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\User;
use App\Models\Department;
use App\Models\Worker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_load_successfully(): void
    {
        $this->get('/')->assertOk();
        $this->get('/login')->assertOk();
        $this->get('/signup')->assertRedirect('/register');
    }

    public function test_admin_dashboard_requires_authentication(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_admin_dashboard_loads_for_municipal_manager(): void
    {
        $admin = User::query()->create([
            'name' => 'Municipal Manager',
            'email' => 'manager@example.com',
            'password' => 'password123',
            'role' => User::ROLE_MUNICIPAL_MANAGER,
        ]);

        $this->get('/citizen')->assertOk();
        $this->get('/worker')->assertOk();
        $this->actingAs($admin)->get('/admin')->assertOk();
        $this->actingAs($admin)->get('/admin/workers')->assertOk();
        $this->actingAs($admin)->get('/admin/departments')->assertOk();
        $this->actingAs($admin)->get('/admin/issues')->assertOk();
    }

    public function test_citizen_dashboard_api_returns_authenticated_user_data(): void
    {
        $citizen = User::query()->create([
            'name' => 'Citizen User',
            'email' => 'citizen@example.com',
            'password' => 'password123',
            'role' => User::ROLE_CITIZEN,
            'preferred_location' => 'Test Ward',
        ]);

        Issue::query()->create([
            'user_id' => $citizen->id,
            'title' => 'Broken streetlight',
            'description' => 'Streetlight is out near the market.',
            'category' => 'lighting',
            'status' => 'reported',
            'priority' => 'medium',
            'latitude' => 18.5204300,
            'longitude' => 73.8567440,
            'address' => 'Market Road',
        ]);

        Sanctum::actingAs($citizen);

        $this->getJson('/api/citizen/dashboard')
            ->assertOk()
            ->assertJsonPath('data.profile.name', 'Citizen User')
            ->assertJsonPath('data.stats.total_reported', 1)
            ->assertJsonPath('data.profile.preferred_location', 'Test Ward');
    }

    public function test_citizen_settings_can_be_updated(): void
    {
        $citizen = User::query()->create([
            'name' => 'Citizen User',
            'email' => 'citizen-settings@example.com',
            'password' => 'password123',
            'role' => User::ROLE_CITIZEN,
        ]);

        Sanctum::actingAs($citizen);

        $this->putJson('/api/citizen/settings', [
            'theme_preference' => 'dark',
        ])
            ->assertOk()
            ->assertJsonPath('data.settings.theme_preference', 'dark');
    }

    public function test_worker_dashboard_and_settings_use_authenticated_worker_data(): void
    {
        $department = Department::query()->create([
            'name' => 'Sanitation',
            'code' => 'SANIT',
        ]);

        $worker = Worker::query()->create([
            'name' => 'Worker User',
            'email' => 'worker@example.com',
            'password' => 'password123',
            'department_id' => $department->id,
            'status' => Worker::STATUS_ACTIVE,
            'theme_preference' => 'light',
        ]);

        $citizen = User::query()->create([
            'name' => 'Issue Reporter',
            'email' => 'reporter@example.com',
            'password' => 'password123',
            'role' => User::ROLE_CITIZEN,
        ]);

        Issue::query()->create([
            'user_id' => $citizen->id,
            'title' => 'Overflowing bin',
            'description' => 'Needs urgent cleanup.',
            'category' => 'sanitation',
            'status' => 'in_progress',
            'priority' => 'high',
            'worker_id' => $worker->id,
            'latitude' => 18.5204300,
            'longitude' => 73.8567440,
            'address' => 'Station Road',
        ]);

        Sanctum::actingAs($worker);

        $this->getJson('/api/worker/dashboard')
            ->assertOk()
            ->assertJsonPath('data.profile.name', 'Worker User')
            ->assertJsonPath('data.stats.in_progress', 1);

        $this->putJson('/api/worker/settings', [
            'theme_preference' => 'dark',
            'preferred_zone' => 'Central Ward',
            'shift_window' => '08:00 AM - 05:00 PM',
        ])
            ->assertOk()
            ->assertJsonPath('data.settings.theme_preference', 'dark');
    }

    public function test_legacy_worker_user_token_can_resolve_linked_worker_profile(): void
    {
        $department = Department::query()->create([
            'name' => 'Electricity',
            'code' => 'ELECT',
        ]);

        $legacyWorkerUser = User::query()->create([
            'name' => 'Legacy Worker User',
            'email' => 'legacy.worker@example.com',
            'password' => 'password123',
            'role' => User::ROLE_WORKER,
        ]);

        Worker::query()->create([
            'name' => 'Linked Worker Profile',
            'email' => 'legacy.worker@example.com',
            'password' => 'password123',
            'department_id' => $department->id,
            'status' => Worker::STATUS_ACTIVE,
            'theme_preference' => 'dark',
        ]);

        Sanctum::actingAs($legacyWorkerUser);

        $this->getJson('/api/worker/settings')
            ->assertOk()
            ->assertJsonPath('data.settings.theme_preference', 'dark');

        $this->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('data.user.role', 'worker')
            ->assertJsonPath('data.user.department.name', 'Electricity');
    }

    public function test_legacy_worker_user_without_worker_row_gets_provisioned(): void
    {
        $department = Department::query()->create([
            'name' => 'Roads',
            'code' => 'ROADS',
        ]);

        $legacyWorkerUser = User::query()->create([
            'name' => 'Provisioned Worker',
            'email' => 'provision.worker@example.com',
            'password' => 'password123',
            'role' => User::ROLE_WORKER,
            'availability_status' => 'busy',
            'theme_preference' => 'dark',
            'preferred_zone' => 'North Ward',
            'shift_window' => '07:00 AM - 03:00 PM',
        ]);

        Sanctum::actingAs($legacyWorkerUser);

        $this->getJson('/api/worker/settings')
            ->assertOk()
            ->assertJsonPath('data.settings.theme_preference', 'dark')
            ->assertJsonPath('data.settings.preferred_zone', 'North Ward');

        $this->assertDatabaseHas('workers', [
            'email' => 'provision.worker@example.com',
            'department_id' => $department->id,
            'theme_preference' => 'dark',
        ]);
    }
}
