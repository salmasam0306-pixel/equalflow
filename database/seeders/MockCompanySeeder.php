<?php
// database/seeders/MockCompanySeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Organization;
use App\Models\OrganizationMember;
use App\Models\Department;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\Notification;
use App\Models\TaskSubmission;
use Illuminate\Support\Facades\Hash;

class MockCompanySeeder extends Seeder
{
    public function run()
    {
        // ============================================
        // 1. CREATE USERS
        // ============================================

        $users = [
            // Principal
            [
                'name' => 'Dr. Ahmad Rizal',
                'email' => 'ahmad@creativesolutions.com',
                'role' => 'principal',
                'job_scope' => 'principal',
                'specialties' => ['Project Management', 'Strategic Planning', 'Business Development'],
            ],
            // Leaders (engineers)
            [
                'name' => 'Sarah Tan',
                'email' => 'sarah@creativesolutions.com',
                'role' => 'leader',
                'job_scope' => 'civil_engineer',
                'specialties' => ['Road Design', 'Drainage System', 'AutoCAD', 'Civil 3D'],
            ],
            [
                'name' => 'Mike Johnson',
                'email' => 'mike@creativesolutions.com',
                'role' => 'leader',
                'job_scope' => 'structural_engineer',
                'specialties' => ['Structural Design', 'Steel Structure', 'Reinforced Concrete', 'ETABS'],
            ],
            // Civil Engineers
            ['name' => 'Aina Zulkifli', 'email' => 'aina@creativesolutions.com', 'role' => 'member', 'job_scope' => 'civil_engineer', 'specialties' => ['Road Design', 'Highway Engineering', 'AutoCAD']],
            ['name' => 'David Chen',    'email' => 'david@creativesolutions.com', 'role' => 'member', 'job_scope' => 'civil_engineer', 'specialties' => ['Drainage System', 'Water Reticulation', 'Civil 3D']],
            ['name' => 'Nurul Izzati', 'email' => 'nurul@creativesolutions.com', 'role' => 'member', 'job_scope' => 'civil_engineer', 'specialties' => ['Earthworks', 'Slope Design', 'Site Grading']],
            // Structural Engineers
            ['name' => 'James Lee',        'email' => 'james@creativesolutions.com', 'role' => 'member', 'job_scope' => 'structural_engineer', 'specialties' => ['Reinforced Concrete', 'Foundation Design', 'Piling']],
            ['name' => 'Siti Norhidayah',  'email' => 'siti@creativesolutions.com',  'role' => 'member', 'job_scope' => 'structural_engineer', 'specialties' => ['Steel Structure', 'STAAD Pro', 'Pre-stressed Concrete']],
            // Drafters
            ['name' => 'Raj Kumar',     'email' => 'raj@creativesolutions.com',     'role' => 'member', 'job_scope' => 'drafter', 'specialties' => ['AutoCAD', 'Civil 3D', 'Revit', 'MicroStation']],
            ['name' => 'Fatimah Zahra', 'email' => 'fatimah@creativesolutions.com', 'role' => 'member', 'job_scope' => 'drafter', 'specialties' => ['AutoCAD', 'BIM Modeling', 'SketchUp']],
            // IOW (engineers)
            ['name' => 'Azman Ibrahim', 'email' => 'azman@creativesolutions.com', 'role' => 'member', 'job_scope' => 'iow_road',    'specialties' => ['Road Design', 'Highway Engineering', 'Site Supervision', 'Quality Control']],
            ['name' => 'Susan Wong',    'email' => 'susan@creativesolutions.com', 'role' => 'member', 'job_scope' => 'iow_drainage','specialties' => ['Drainage System', 'Water Reticulation', 'Sewerage System', 'Site Supervision']],
            // Project Managers
            ['name' => 'Hazirah Mohd', 'email' => 'hazirah@creativesolutions.com', 'role' => 'member', 'job_scope' => 'project_manager', 'specialties' => ['Project Management', 'Scheduling', 'Cost Estimation', 'Contract Administration']],
            ['name' => 'John Tan',     'email' => 'john@creativesolutions.com',     'role' => 'member', 'job_scope' => 'project_manager', 'specialties' => ['Project Management', 'Risk Management', 'Quality Control']],
            // Clerk
            ['name' => 'Mariam Jamil', 'email' => 'mariam@creativesolutions.com', 'role' => 'member', 'job_scope' => 'clerk', 'specialties' => ['Documentation', 'Submissions', 'OSC', 'BOMBA', 'JPS', 'IWK']],
        ];

        foreach ($users as $userData) {
            if (!User::where('email', $userData['email'])->exists()) {
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password123'),
                    'role' => $userData['role'],
                    'job_scope' => $userData['job_scope'],
                    'specialties' => $userData['specialties'],
                    'is_profile_ready' => true,
                    'profile_completed_at' => now(),
                    'email_verified_at' => now(),
                ]);
            }
        }

        $this->command->info('✅ ' . count($users) . ' users created!');

        // ============================================
        // 2. CREATE COMPANY
        // ============================================

        $principal = User::where('email', 'ahmad@creativesolutions.com')->first();

        $company = Organization::where('name', 'Creative Solutions Engineering Sdn Bhd')->first();
        if (!$company) {
            $company = Organization::create([
                'name' => 'Creative Solutions Engineering Sdn Bhd',
                'description' => 'Leading civil and structural engineering consultancy firm specializing in infrastructure and building projects.',
                'industry' => 'Engineering & Construction',
                'invite_code' => 'CS2024',
                'created_by' => $principal->id,
            ]);
            $this->command->info('✅ Company created: ' . $company->name);
        } else {
            $this->command->info('ℹ️ Company already exists: ' . $company->name);
        }

        // ============================================
        // 3. ADD ALL USERS TO COMPANY
        // ============================================

        foreach (User::all() as $user) {
            if ($user->isPersonal()) continue;

            $role = match ($user->role) {
                'principal' => 'principal',
                'leader'    => 'leader',
                default     => 'member',
            };

            OrganizationMember::firstOrCreate(
                ['organization_id' => $company->id, 'user_id' => $user->id],
                ['role' => $role]
            );
        }

        $this->command->info('✅ All users added to company!');

        // ============================================
        // 4. CREATE DEPARTMENTS
        // ============================================

        $departmentsData = [
            [
                'name'    => 'Civil Engineering',
                'members' => [
                    'sarah@creativesolutions.com',
                    'aina@creativesolutions.com',
                    'david@creativesolutions.com',
                    'nurul@creativesolutions.com',
                ],
            ],
            [
                'name'    => 'Structural Engineering',
                'members' => [
                    'mike@creativesolutions.com',
                    'james@creativesolutions.com',
                    'siti@creativesolutions.com',
                ],
            ],
            [
                'name'    => 'Shared Services',
                'members' => [
                    'raj@creativesolutions.com',
                    'fatimah@creativesolutions.com',
                    'hazirah@creativesolutions.com',
                    'john@creativesolutions.com',
                    'mariam@creativesolutions.com',
                    'azman@creativesolutions.com',
                    'susan@creativesolutions.com',
                ],
            ],
        ];

        foreach ($departmentsData as $deptData) {
            $dept = Department::where('organization_id', $company->id)
                ->where('name', $deptData['name'])
                ->first();

            if (!$dept) {
                $dept = Department::create([
                    'organization_id' => $company->id,
                    'name'            => $deptData['name'],
                    'description'     => null,
                    'created_by'      => $principal->id,
                    'status'          => 'active',
                ]);

                $this->command->info("✅ Created department: {$dept->name}");
            }

            foreach ($deptData['members'] as $email) {
                $user = User::where('email', $email)->first();
                if ($user && !$dept->isMember($user)) {
                    $dept->addMember($user);
                }
            }
        }

        $this->command->info('✅ Departments created and members assigned!');

        // ============================================
        // 5. CREATE PROJECTS
        // ============================================

        $projectsData = [
            ['name' => 'Taman Mutiara Housing Development', 'description' => 'Comprehensive infrastructure development for Taman Mutiara housing project.', 'deadline' => now()->addMonths(6)],
            ['name' => 'Kuala Lumpur City Center Mall',     'description' => 'Structural design and construction supervision for a 5-story shopping mall.', 'deadline' => now()->addMonths(8)],
            ['name' => 'Putrajaya Road Network Upgrade',    'description' => 'Upgrading major road networks in Putrajaya including highway design.',        'deadline' => now()->addMonths(4)],
            ['name' => 'Penang Bridge Expansion',           'description' => 'Structural analysis and design for the Penang Bridge expansion project.',     'deadline' => now()->addMonths(12)],
            ['name' => 'Johor Industrial Park',             'description' => 'Complete civil engineering design for a new industrial park.',                'deadline' => now()->addMonths(5)],
        ];

        foreach ($projectsData as $projectData) {
            $existing = Project::where('name', $projectData['name'])
                ->where('organization_id', $company->id)
                ->first();

            if (!$existing) {
                $project = Project::create([
                    'name'            => $projectData['name'],
                    'description'     => $projectData['description'],
                    'organization_id' => $company->id,
                    'created_by'      => $principal->id,
                    'leader_id'       => null,
                    'type'            => 'company',
                    'status'          => 'active',
                    'start_date'      => now()->subDays(30),
                    'deadline'        => $projectData['deadline'],
                ]);
                $this->command->info("✅ Created project: {$project->name}");
            }
        }

        // ============================================
        // 6. CREATE PERSONAL PROJECTS
        // ============================================

        $personalProjects = [
            ['user' => 'sarah@creativesolutions.com', 'name' => 'Professional Development - BIM Certification', 'description' => 'Learning and preparing for BIM certification exam.'],
            ['user' => 'james@creativesolutions.com', 'name' => 'Research - Sustainable Foundation Systems',    'description' => 'Research paper on sustainable foundation systems.'],
            ['user' => 'nurul@creativesolutions.com', 'name' => 'Side Project - Slope Stability App',           'description' => 'Developing a mobile app for slope stability analysis.'],
        ];

        foreach ($personalProjects as $pp) {
            $user = User::where('email', $pp['user'])->first();

            $existing = Project::where('name', $pp['name'])
                ->where('created_by', $user->id)
                ->where('type', 'personal')
                ->first();

            if (!$existing) {
                Project::create([
                    'name'        => $pp['name'],
                    'description' => $pp['description'],
                    'created_by'  => $user->id,
                    'leader_id'   => $user->id,
                    'type'        => 'personal',
                    'status'      => 'active',
                    'start_date'  => now()->subDays(10),
                    'deadline'    => now()->addDays(60),
                    'invite_code' => 'P' . strtoupper(substr(md5(rand()), 0, 5)),
                ]);
                // Creator is auto-attached by Project::boot() — no manual attach.
            }
        }

        $this->command->info('✅ Personal projects processed!');

        // ============================================
        // 7. SUMMARY
        // ============================================

        $this->command->line('');
        $this->command->line('============================================');
        $this->command->line('🎉 MOCK COMPANY CREATED SUCCESSFULLY!');
        $this->command->line('============================================');
        $this->command->line('');
        $this->command->line('📊 Company Statistics:');
        $this->command->line('   • Users: ' . User::count());
        $this->command->line('   • Departments: ' . Department::where('organization_id', $company->id)->count());
        $this->command->line('   • Projects: ' . Project::where('type', 'company')->count());
        $this->command->line('');
        $this->command->line('🔑 Login Credentials (password: password123):');
        $this->command->line('   Principal: ahmad@creativesolutions.com');
        $this->command->line('   Leader:    sarah@creativesolutions.com (Civil)');
        $this->command->line('   Leader:    mike@creativesolutions.com  (Structural)');
        $this->command->line('   Member:    aina@creativesolutions.com  (Civil)');
        $this->command->line('   Member:    raj@creativesolutions.com   (Drafter)');
        $this->command->line('');
        $this->command->line('📁 Company Invite Code: CS2024');
        $this->command->line('');
        $this->command->line('🔗 Access: http://equalflow.test/login');
        $this->command->line('');
        $this->command->line('Next: php artisan db:seed --class=OngoingProjectsSeeder');
        $this->command->line('============================================');
    }
}