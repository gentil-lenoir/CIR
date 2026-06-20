<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Department;
use App\Models\Issue;
use App\Models\Notification;
use App\Models\Upvote;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        DB::transaction(function (): void {
            Notification::query()->delete();
            Comment::query()->delete();
            Upvote::query()->delete();

            Issue::query()
                ->where('title', 'Large pothole causing traffic jam')
                ->delete();

            Issue::query()->update([
                'worker_id' => null,
                'deadline' => null,
                'escalated_to' => null,
                'escalated_at' => null,
            ]);

            Worker::query()->delete();

            User::query()
                ->where('role', User::ROLE_SYSTEM_MANAGER)
                ->delete();

            User::query()
                ->where('role', User::ROLE_MUNICIPAL_MANAGER)
                ->where('email', '!=', 'admin2004@gmail.com')
                ->delete();

            $municipalManager = User::query()->updateOrCreate(
                ['email' => 'admin2004@gmail.com'],
                [
                    'name' => 'Aarav Municipal Manager',
                    'password' => Hash::make('admin123'),
                    'role' => User::ROLE_MUNICIPAL_MANAGER,
                    'phone' => '9000000001',
                    'availability_status' => 'available',
                    'email_verified_at' => now(),
                ]
            );

            $citizen = User::query()->updateOrCreate(
                ['email' => 'citizen.demo@civic.local'],
                [
                    'name' => 'Rahul Citizen',
                    'password' => Hash::make('citizen123'),
                    'role' => User::ROLE_CITIZEN,
                    'phone' => '9000000003',
                    'preferred_location' => 'Green Park Road, Pune',
                    'theme_preference' => 'light',
                    'notify_email' => true,
                    'notify_sms' => false,
                    'community_alerts' => true,
                    'availability_status' => 'available',
                    'email_verified_at' => now(),
                ]
            );

            $sanitation = $this->upsertDepartment('Sanitation', 'Waste collection and public area cleanliness.');
            $electricity = $this->upsertDepartment('Electricity', 'Streetlight and electrical maintenance.');
            $waterSupply = $this->upsertDepartment('Water Supply', 'Pipe leakage and water pressure support.');

            $workerOne = Worker::query()->updateOrCreate(
                ['email' => 'worker.sanitation@civic.local'],
                [
                    'name' => 'Priya Sanitation',
                    'phone' => '9000000101',
                    'department_id' => $sanitation->id,
                    'password' => Hash::make('worker123'),
                    'status' => Worker::STATUS_ACTIVE,
                    'availability_status' => 'available',
                    'theme_preference' => 'light',
                    'preferred_zone' => 'Market Corner Ward',
                    'shift_window' => '08:00 AM - 05:00 PM',
                    'notify_new_assignments' => true,
                    'notify_escalation_alerts' => true,
                    'notify_daily_summary' => false,
                ]
            );

            $workerTwo = Worker::query()->updateOrCreate(
                ['email' => 'worker.electricity@civic.local'],
                [
                    'name' => 'Arjun Electricity',
                    'phone' => '9000000102',
                    'department_id' => $electricity->id,
                    'password' => Hash::make('worker123'),
                    'status' => Worker::STATUS_ACTIVE,
                    'availability_status' => 'busy',
                    'theme_preference' => 'dark',
                    'preferred_zone' => 'Green Park Sector',
                    'shift_window' => '09:00 AM - 06:00 PM',
                    'notify_new_assignments' => true,
                    'notify_escalation_alerts' => true,
                    'notify_daily_summary' => true,
                ]
            );

            $workerThree = Worker::query()->updateOrCreate(
                ['email' => 'worker.water@civic.local'],
                [
                    'name' => 'Meera Water',
                    'phone' => '9000000103',
                    'department_id' => $waterSupply->id,
                    'password' => Hash::make('worker123'),
                    'status' => Worker::STATUS_INACTIVE,
                    'availability_status' => 'offline',
                    'theme_preference' => 'light',
                    'preferred_zone' => 'Ward Office Lane',
                    'shift_window' => '10:00 AM - 06:00 PM',
                    'notify_new_assignments' => false,
                    'notify_escalation_alerts' => false,
                    'notify_daily_summary' => false,
                ]
            );

            $reportedIssue = Issue::query()->updateOrCreate(
                ['user_id' => $citizen->id, 'title' => 'Streetlight not working near park'],
                [
                    'description' => 'The streetlight near Green Park has been off for two nights.',
                    'category' => 'electricity',
                    'status' => 'reported',
                    'priority' => 'medium',
                    'latitude' => 18.5204300,
                    'longitude' => 73.8567440,
                    'address' => 'Green Park Road, Pune',
                    'reported_at' => now()->subDays(2),
                ]
            );

            $inProgressIssue = Issue::query()->updateOrCreate(
                ['user_id' => $citizen->id, 'title' => 'Overflowing roadside garbage bin'],
                [
                    'description' => 'The garbage bin at the market corner is overflowing onto the road.',
                    'category' => 'sanitation',
                    'status' => 'in_progress',
                    'priority' => 'high',
                    'worker_id' => $workerOne->id,
                    'deadline' => now()->addDay()->toDateString(),
                    'latitude' => 18.5215000,
                    'longitude' => 73.8571000,
                    'address' => 'Market Corner, Pune',
                    'reported_at' => now()->subDay(),
                ]
            );

            $resolvedIssue = Issue::query()->updateOrCreate(
                ['user_id' => $citizen->id, 'title' => 'Water leakage beside ward office'],
                [
                    'description' => 'Continuous water leakage is making the road slippery.',
                    'category' => 'water',
                    'status' => 'resolved',
                    'priority' => 'urgent',
                    'worker_id' => $workerTwo->id,
                    'deadline' => now()->subDay()->toDateString(),
                    'latitude' => 18.5199000,
                    'longitude' => 73.8558000,
                    'address' => 'Ward Office Lane, Pune',
                    'citizen_feedback' => 'Leakage was fixed quickly. Thank you.',
                    'feedback_submitted_at' => now()->subHours(6),
                    'reported_at' => now()->subDays(4),
                ]
            );

            Comment::query()->firstOrCreate(
                ['issue_id' => $inProgressIssue->id, 'user_id' => $citizen->id, 'comment' => 'Please resolve this before the evening crowd arrives.']
            );

            Comment::query()->firstOrCreate(
                ['issue_id' => $resolvedIssue->id, 'user_id' => $citizen->id, 'comment' => 'The repair looks good now.']
            );

            Upvote::query()->firstOrCreate(
                ['issue_id' => $reportedIssue->id, 'user_id' => $municipalManager->id]
            );

            $reportedIssue->update(['upvotes_count' => 1]);

            Notification::query()->firstOrCreate(
                ['user_id' => $citizen->id, 'issue_id' => $resolvedIssue->id, 'type' => 'issue_status_updated'],
                [
                    'title' => 'Complaint Resolved',
                    'message' => "Your complaint #{$resolvedIssue->id} has been marked resolved.",
                    'data' => ['to_status' => 'resolved'],
                ]
            );

            Notification::query()->firstOrCreate(
                ['user_id' => $citizen->id, 'issue_id' => $inProgressIssue->id, 'type' => 'issue_status_updated'],
                [
                    'title' => 'Complaint In Progress',
                    'message' => "Your complaint #{$inProgressIssue->id} is now in progress.",
                    'data' => ['to_status' => 'in_progress'],
                ]
            );
        });
    }

    private function upsertDepartment(string $name, string $description): Department
    {
        return Department::query()->updateOrCreate(
            ['name' => $name],
            [
                'code' => strtoupper(str_replace(' ', '', substr($name, 0, 8))),
                'description' => $description,
            ]
        );
    }
}
