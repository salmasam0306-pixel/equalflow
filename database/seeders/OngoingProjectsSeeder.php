<?php
// database/seeders/OngoingProjectsSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Project;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\Organization;
use App\Models\Department;
use App\Models\Notification;
use App\Models\TaskDependency;
use Illuminate\Support\Facades\Storage;

class OngoingProjectsSeeder extends Seeder
{
    public function run()
    {
        $company = Organization::where('name', 'Creative Solutions Engineering Sdn Bhd')->first();

        if (!$company) {
            $this->command->error('Please run MockCompanySeeder first!');
            return;
        }

        $principal = User::where('email', 'ahmad@creativesolutions.com')->first();

        // Look up departments
        $civilDept  = Department::where('organization_id', $company->id)->where('name', 'Civil Engineering')->first();
        $structDept = Department::where('organization_id', $company->id)->where('name', 'Structural Engineering')->first();
        $sharedDept = Department::where('organization_id', $company->id)->where('name', 'Shared Services')->first();

        if (!$civilDept || !$structDept || !$sharedDept) {
            $this->command->error('Departments missing! Run MockCompanySeeder first.');
            return;
        }

        $this->command->info('Creating ongoing and completed projects with main/sub tasks...');

        // ============================================
        // PROJECT 1 — Cyberjaya Smart City (ACTIVE)
        // ============================================
        $cyberjaya = $this->createProject($company, $principal, [
            'name'        => 'Cyberjaya Smart City Infrastructure',
            'description' => 'Smart city infrastructure project including intelligent traffic systems, smart drainage, and IoT-enabled utilities.',
            'start_date'  => now()->subMonths(2),
            'deadline'    => now()->addMonths(4),
            'status'      => 'active',
        ]);

        if ($cyberjaya->mainTasks()->count() === 0) {
            $cyberjayaMain = $this->createMainTask($cyberjaya, $civilDept, [
                'title'         => 'Smart Drainage & Traffic Systems',
                'description'   => 'Design IoT-enabled drainage and intelligent traffic systems for Cyberjaya.',
                'leader_email'  => 'sarah@creativesolutions.com',
                'priority'      => 'high',
                'start_date'    => now()->subMonths(2),
                'due_date'      => now()->addMonths(3),
                'skills'        => ['Road Design', 'Drainage System', 'Highway Engineering'],
            ]);

            $this->createSubtask($cyberjayaMain, $civilDept,  'Feasibility Study',                'sarah@creativesolutions.com',  'done',        now()->subDays(40), now()->subDays(10));
            $this->createSubtask($cyberjayaMain, $sharedDept, 'Smart Drainage Design',           'susan@creativesolutions.com',  'done',        now()->subDays(35), now()->subDays(5));
            $this->createSubtask($cyberjayaMain, $civilDept,  'Intelligent Traffic System Design','azman@creativesolutions.com',  'in_progress', now()->subDays(25), now()->addDays(20));
            $this->createSubtask($cyberjayaMain, $sharedDept, 'IoT Utility Network Design',      'david@creativesolutions.com',  'in_progress', now()->subDays(20), now()->addDays(15));
            $this->createSubtask($cyberjayaMain, $sharedDept, 'Smart City Dashboard Development','raj@creativesolutions.com',    'todo',        null,               now()->addDays(30));
            $this->createSubtask($cyberjayaMain, $sharedDept, 'Cybersecurity Risk Assessment',   'john@creativesolutions.com',   'todo',        null,               now()->addDays(45));
            $this->createSubtask($cyberjayaMain, $structDept, 'Master Plan Review',              'mike@creativesolutions.com',   'review',      now()->subDays(5),  now()->addDays(5));

            $this->command->info('  ✅ Cyberjaya main task + 7 sub-tasks created');
        }

        // ============================================
        // PROJECT 2 — Seremban Hospital (ACTIVE)
        // ============================================
        $seremban = $this->createProject($company, $principal, [
            'name'        => 'Seremban Central Hospital Extension',
            'description' => 'Extension and renovation including new wing, parking structure, and emergency department upgrade.',
            'start_date'  => now()->subMonths(3),
            'deadline'    => now()->addMonths(6),
            'status'      => 'active',
        ]);

        if ($seremban->mainTasks()->count() === 0) {
            $serembanMain = $this->createMainTask($seremban, $structDept, [
                'title'         => 'Structural Design — New Wing',
                'description'   => 'Structural analysis and design for the new hospital wing and parking structure.',
                'leader_email'  => 'mike@creativesolutions.com',
                'priority'      => 'high',
                'start_date'    => now()->subMonths(3),
                'due_date'      => now()->addMonths(5),
                'skills'        => ['Structural Design', 'Reinforced Concrete', 'Foundation Design'],
            ]);

            $this->createSubtask($serembanMain, $structDept, 'Site Survey and Assessment',   'james@creativesolutions.com', 'done',        now()->subDays(45), now()->subDays(15));
            $this->createSubtask($serembanMain, $structDept, 'Structural Load Analysis',     'siti@creativesolutions.com',  'done',        now()->subDays(40), now()->subDays(7));
            $this->createSubtask($serembanMain, $structDept, 'Emergency Department Design',  'siti@creativesolutions.com',  'in_progress', now()->subDays(20), now()->addDays(25));
            $this->createSubtask($serembanMain, $structDept, 'Parking Structure Design',     'james@creativesolutions.com', 'in_progress', now()->subDays(10), now()->addDays(35));
            $this->createSubtask($serembanMain, $sharedDept, 'MEP Systems Integration',      'hazirah@creativesolutions.com','todo',        null,               now()->addDays(50));
            $this->createSubtask($serembanMain, $structDept, 'Safety Compliance Review',     'mike@creativesolutions.com',  'review',      now()->subDays(4),  now()->addDays(3));

            $this->command->info('  ✅ Seremban main task + 6 sub-tasks created');
        }

        // ============================================
        // PROJECT 3 — Johor Industrial Park (COMPLETED)
        // ============================================
        $johor = $this->createProject($company, $principal, [
            'name'        => 'Johor Industrial Park',
            'description' => 'Complete civil engineering design for a new industrial park including roads, drainage, and earthworks. Delivered and closed.',
            'start_date'  => now()->subMonths(6),
            'deadline'    => now()->subDays(10),
            'status'      => 'completed',
        ]);

        if ($johor->mainTasks()->count() === 0) {
            // -- Main task 1: Site Infrastructure (Civil)
            $main1 = $this->createMainTask($johor, $civilDept, [
                'title'         => 'Site Infrastructure Design',
                'description'   => 'Road, drainage and earthworks design for the industrial park.',
                'leader_email'  => 'sarah@creativesolutions.com',
                'priority'      => 'high',
                'start_date'    => now()->subMonths(6),
                'due_date'      => now()->subMonths(2),
                'skills'        => ['Road Design', 'Drainage System', 'Earthworks'],
            ]);

            $this->createSubtask($main1, $civilDept,  'Site Survey',                'aina@creativesolutions.com',  'done', now()->subMonths(5), now()->subMonths(5));
            $this->createSubtask($main1, $civilDept,  'Access Road Design',         'aina@creativesolutions.com',  'done', now()->subMonths(5), now()->subMonths(3));
            $this->createSubtask($main1, $civilDept,  'Drainage System Design',     'david@creativesolutions.com', 'done', now()->subMonths(4), now()->subMonths(3));
            $this->createSubtask($main1, $civilDept,  'Earthworks and Grading',     'nurul@creativesolutions.com', 'done', now()->subMonths(4), now()->subMonths(2));
            $this->createSubtask($main1, $sharedDept, 'Road Drawings (Drafting)',   'raj@creativesolutions.com',   'done', now()->subMonths(4), now()->subMonths(2));
            $this->createSubtask($main1, $sharedDept, 'QC Review of Design Package','susan@creativesolutions.com', 'done', now()->subMonths(3), now()->subMonths(2));

            // -- Main task 2: Structural Design (Structural) — depends on main1
            $main2 = $this->createMainTask($johor, $structDept, [
                'title'         => 'Structural Design — Warehouse Frame',
                'description'   => 'Steel structure design and foundation analysis for warehouse buildings.',
                'leader_email'  => 'mike@creativesolutions.com',
                'priority'      => 'medium',
                'start_date'    => now()->subMonths(5),
                'due_date'      => now()->subMonths(1),
                'skills'        => ['Steel Structure', 'Foundation Design', 'Reinforced Concrete'],
                'depends_on'    => [$main1],
            ]);

            $this->createSubtask($main2, $structDept, 'Load Analysis',              'siti@creativesolutions.com',  'done', now()->subMonths(4), now()->subMonths(3));
            $this->createSubtask($main2, $structDept, 'Steel Frame Design',         'siti@creativesolutions.com',  'done', now()->subMonths(3), now()->subMonths(2));
            $this->createSubtask($main2, $structDept, 'Foundation Design',          'james@creativesolutions.com', 'done', now()->subMonths(3), now()->subMonths(2));
            $this->createSubtask($main2, $sharedDept, 'Structural Drawings',        'fatimah@creativesolutions.com','done',now()->subMonths(2), now()->subMonths(1));
            $this->createSubtask($main2, $sharedDept, 'QC Structural Package',      'john@creativesolutions.com',  'done', now()->subMonths(1), now()->subMonths(1));

            // -- Main task 3: Documentation — depends on main1 + main2
            $main3 = $this->createMainTask($johor, $civilDept, [
                'title'         => 'Authority Submissions & Documentation',
                'description'   => 'Prepare and submit documentation to all relevant authorities.',
                'leader_email'  => 'sarah@creativesolutions.com',
                'priority'      => 'medium',
                'start_date'    => now()->subMonths(4),
                'due_date'      => now()->subDays(15),
                'skills'        => ['Documentation', 'Submissions', 'OSC', 'BOMBA'],
                'depends_on'    => [$main1, $main2],
            ]);

            $this->createSubtask($main3, $sharedDept, 'OSC Submission',   'mariam@creativesolutions.com', 'done', now()->subMonths(3), now()->subMonths(2));
            $this->createSubtask($main3, $sharedDept, 'BOMBA Submission', 'mariam@creativesolutions.com', 'done', now()->subMonths(2), now()->subMonths(1));
            $this->createSubtask($main3, $sharedDept, 'JPS Submission',   'mariam@creativesolutions.com', 'done', now()->subMonths(1), now()->subDays(15));

            // -- Sample approved submissions
            $this->attachSampleSubmission($main1->subtasks()->where('title', 'Access Road Design')->first(), 'aina@creativesolutions.com', 'sarah@creativesolutions.com', 'road-design.pdf');
            $this->attachSampleSubmission($main1->subtasks()->where('title', 'Drainage System Design')->first(), 'david@creativesolutions.com', 'sarah@creativesolutions.com', 'drainage-plan.pdf');
            $this->attachSampleSubmission($main2->subtasks()->where('title', 'Steel Frame Design')->first(), 'siti@creativesolutions.com', 'mike@creativesolutions.com', 'steel-frame.pdf');
            $this->attachSampleSubmission($main3->subtasks()->where('title', 'OSC Submission')->first(), 'mariam@creativesolutions.com', 'sarah@creativesolutions.com', 'osc-pack.pdf');

            $this->command->info('  ✅ Johor main tasks (3) + 14 sub-tasks created · project COMPLETED');
            $this->command->info('     └─ Structural Design depends on Site Infrastructure');
            $this->command->info('     └─ Authority Submissions depends on Site Infrastructure + Structural Design');
        }

        // ============================================
        // SUB-TASK DEPENDENCIES
        // ============================================
        $this->createDependencies();

        // ============================================
        // SUMMARY
        // ============================================
        $completedTasks = Task::where('status', 'done')->count();
        $inProgress     = Task::where('status', 'in_progress')->count();
        $review         = Task::where('status', 'review')->count();
        $unassigned     = Task::whereNull('assigned_to')->where('status', 'todo')->count();
        $mainDeps       = TaskDependency::whereHas('task', fn ($q) => $q->where('task_type', Task::TYPE_MAIN))->count();

        $this->command->line('');
        $this->command->line('============================================');
        $this->command->line('📊 TASK STATUS SUMMARY');
        $this->command->line('============================================');
        $this->command->line("   ✅ Done:                $completedTasks");
        $this->command->line("   🔄 In Progress:         $inProgress");
        $this->command->line("   👁️  Review:              $review");
        $this->command->line("   📥 Unassigned:          $unassigned");
        $this->command->line("   🔗 Main-task deps:      $mainDeps");
        $this->command->line('');
        $this->command->line('🎉 Projects seeded!');
        $this->command->line('   • Cyberjaya Smart City (active)');
        $this->command->line('   • Seremban Hospital   (active)');
        $this->command->line('   • Johor Industrial Park (COMPLETED — report unlocked)');
        $this->command->line('============================================');
    }

    // =================================================
    // HELPERS
    // =================================================

    private function createProject(Organization $company, User $principal, array $data): Project
    {
        $project = Project::firstOrCreate(
            ['name' => $data['name'], 'organization_id' => $company->id],
            [
                'description'     => $data['description'],
                'created_by'      => $principal->id,
                'type'            => 'company',
                'status'          => $data['status'],
                'start_date'      => $data['start_date'] ?? now()->subDays(30),
                'deadline'        => $data['deadline'] ?? now()->addMonths(3),
            ]
        );

        // If project already existed (created by MockCompanySeeder with status=active),
        // force its status/start_date/deadline to match this seeder's intent.
        if (!$project->wasRecentlyCreated) {
            $project->update([
                'status'     => $data['status'],
                'start_date' => $data['start_date'] ?? $project->start_date,
                'deadline'   => $data['deadline']   ?? $project->deadline,
            ]);
        }

        return $project;
    }

    private function createMainTask(Project $project, Department $dept, array $data): Task
    {
        $leader = User::where('email', $data['leader_email'])->first();

        $task = Task::firstOrCreate(
            ['title' => $data['title'], 'project_id' => $project->id],
            [
                'parent_task_id'   => null,
                'task_type'        => Task::TYPE_MAIN,
                'department_id'    => $dept->id,
                'assigned_to'      => $leader->id,
                'assigned_by'      => $project->created_by,
                'created_by'       => $project->created_by,
                'description'      => $data['description'],
                'status'           => Task::STATUS_TODO,
                'priority'         => $data['priority'] ?? 'medium',
                'start_date'       => $data['start_date'] ?? null,
                'due_date'         => $data['due_date'] ?? null,
                'skills_required'  => $data['skills'] ?? [],
                'is_assigned'      => true,
                'assigned_at'      => now(),
                'is_blocked'       => false,
            ]
        );

        // Persist main-task dependencies if provided
        if (!empty($data['depends_on'])) {
            foreach ($data['depends_on'] as $blockerTask) {
                if (!$blockerTask instanceof Task) continue;
                if ((int) $blockerTask->id === (int) $task->id) continue;

                TaskDependency::firstOrCreate(
                    [
                        'task_id'            => $task->id,
                        'depends_on_task_id' => $blockerTask->id,
                    ],
                    ['dependency_type' => 'finish_to_start']
                );
            }

            $task->refresh();
            $task->is_blocked = $task->isBlocked();
            $task->save();
        }

        return $task;
    }

    private function createSubtask(Task $parent, Department $dept, string $title, string $assigneeEmail, string $status, $start = null, $due = null, array $skills = []): Task
    {
        $assignee = User::where('email', $assigneeEmail)->first();

        $task = Task::firstOrCreate(
            ['title' => $title, 'parent_task_id' => $parent->id],
            [
                'project_id'      => $parent->project_id,
                'department_id'   => $dept->id,
                'task_type'       => Task::TYPE_SUB,
                'assigned_to'     => $assignee?->id,
                'assigned_by'     => $parent->assigned_to,
                'created_by'      => $parent->assigned_to,
                'description'     => $title,
                'status'          => $status,
                'priority'        => $parent->priority,
                'start_date'      => $start,
                'due_date'        => $due,
                'skills_required' => $skills ?: $parent->skills_required,
                'is_assigned'     => (bool) $assignee,
                'assigned_at'     => $assignee ? now() : null,
                'is_blocked'      => false,
                'completed_at'    => $status === 'done' ? now() : null,
            ]
        );

        return $task;
    }

    private function attachSampleSubmission(Task $subtask, string $submitterEmail, string $reviewerEmail, string $filename): void
    {
        if (!$subtask) return;
        if ($subtask->submissions()->exists()) return;

        $submitter = User::where('email', $submitterEmail)->first();
        $reviewer  = User::where('email', $reviewerEmail)->first();

        if (!$submitter || !$reviewer) return;

        TaskSubmission::create([
            'task_id'      => $subtask->id,
            'submitted_by' => $submitter->id,
            'file_name'    => $filename,
            'file_path'    => 'sample/' . $filename,
            'file_type'    => 'application/pdf',
            'file_size'    => rand(200_000, 2_000_000),
            'description'  => 'Deliverable submitted for review.',
            'status'       => TaskSubmission::STATUS_APPROVED,
            'review_notes' => 'Approved.',
            'reviewed_by'  => $reviewer->id,
            'submitted_at' => now()->subDays(rand(5, 30)),
            'reviewed_at'  => now()->subDays(rand(1, 5)),
            'version'      => 1,
        ]);
    }

    private function createDependencies(): void
    {
        // Cyberjaya: intelligent traffic depends on feasibility study
        $cyberjaya = Project::where('name', 'like', '%Cyberjaya%')->first();
        if ($cyberjaya) {
            $feasibility = Task::where('project_id', $cyberjaya->id)->where('title', 'Feasibility Study')->first();
            $traffic     = Task::where('project_id', $cyberjaya->id)->where('title', 'Intelligent Traffic System Design')->first();

            if ($feasibility && $traffic) {
                TaskDependency::firstOrCreate([
                    'task_id' => $traffic->id,
                    'depends_on_task_id' => $feasibility->id,
                ], ['dependency_type' => 'finish_to_start']);
            }
        }

        // Seremban: emergency design depends on load analysis
        $seremban = Project::where('name', 'like', '%Seremban%')->first();
        if ($seremban) {
            $loadAnalysis = Task::where('project_id', $seremban->id)->where('title', 'Structural Load Analysis')->first();
            $emergency    = Task::where('project_id', $seremban->id)->where('title', 'Emergency Department Design')->first();

            if ($loadAnalysis && $emergency) {
                TaskDependency::firstOrCreate([
                    'task_id' => $emergency->id,
                    'depends_on_task_id' => $loadAnalysis->id,
                ], ['dependency_type' => 'finish_to_start']);
            }
        }
    }
}