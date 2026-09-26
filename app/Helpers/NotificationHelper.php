<?php
// app/Helpers/NotificationHelper.php

namespace App\Helpers;

use App\Models\Task;
use App\Models\TaskSubmission;
use App\Models\TaskSubmissionComment;
use App\Services\NotificationService;
use Illuminate\Support\Facades\App;

/**
 * Static convenience wrapper around NotificationService.
 *
 * Loaded globally via composer.json's autoload.files.
 * Never declare a class here whose FQN collides with a class in App\Services.
 *
 * Design principles:
 *  - Primary recipients get the "someone else did something" notification.
 *  - The ACTOR gets a confirmation receipt in their own bell (activity log),
 *    EXCEPT for administrative actions (department changes) where receipts
 *    would be noise.
 *  - Project notifications are SCOPED to people actually on the project
 *    (creator, leader, active project_members) — never the whole organization.
 */
class NotificationHelper
{
    protected static function service(): NotificationService
    {
        return App::make(NotificationService::class);
    }

    protected static function recipients(array $ids): array
    {
        return collect($ids)
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    // ==========================================================
    // GENERIC
    // ==========================================================

    public static function systemNotification($userId, string $title, string $message, ?string $link = null): void
    {
        if (!$userId) return;

        self::service()->notify($userId, 'system', $title, $message, [
            'link' => $link,
        ]);
    }

    // ==========================================================
    // TASKS
    // ==========================================================

    public static function taskAssigned(Task $task): void
    {
        if (!$task->assigned_to) return;

        $assigneeId = (int) $task->assigned_to;
        $assignerId = $task->assigned_by ? (int) $task->assigned_by : null;
        $actorId    = auth()->id() ? (int) auth()->id() : null;

        $assignerName = $task->assigner?->name ?? 'Your team leader';

        self::service()->notify(
            $assigneeId,
            'task_assigned',
            'New Task Assigned: ' . $task->title,
            $assignerName . ' assigned you a new task.',
            [
                'link'      => route('tasks.submit', $task),
                'sender_id' => $assignerId,
                'task_id'   => $task->id,
            ]
        );

        foreach (self::recipients([$assignerId, $actorId]) as $receiptId) {
            if ($receiptId === $assigneeId) continue;

            self::service()->notify(
                $receiptId,
                'task_assigned',
                'You assigned: ' . $task->title,
                'Assigned to ' . ($task->assignee?->name ?? 'a team member') . '.',
                [
                    'link'      => route('tasks.show', $task),
                    'sender_id' => $actorId,
                    'task_id'   => $task->id,
                ]
            );
        }
    }

    public static function taskSubmitted(Task $task, ?TaskSubmission $submission = null): void
    {
        $submitterId = $submission?->submitted_by
            ? (int) $submission->submitted_by
            : (auth()->id() ? (int) auth()->id() : null);

        $reviewerId = optional($task->parent)->assigned_to
            ?? $task->project?->created_by;
        $reviewerId = $reviewerId ? (int) $reviewerId : null;

        if ($reviewerId && $reviewerId !== $submitterId) {
            self::service()->notify(
                $reviewerId,
                'task_submitted',
                'Task Submitted: ' . $task->title,
                'A task has been submitted for review.',
                [
                    'link'          => route('tasks.submit', $task),
                    'sender_id'     => $submitterId,
                    'task_id'       => $task->id,
                    'submission_id' => $submission?->id,
                ]
            );
        }

        if ($submitterId) {
            self::service()->notify(
                $submitterId,
                'task_submitted',
                'Submission received: ' . $task->title,
                'Your submission has been sent for review.',
                [
                    'link'          => route('tasks.submit', $task),
                    'sender_id'     => $submitterId,
                    'task_id'       => $task->id,
                    'submission_id' => $submission?->id,
                ]
            );
        }
    }

    public static function taskReviewed(Task $task, ?TaskSubmission $submission, string $outcome): void
    {
        $submitterId = $submission?->submitted_by
            ? (int) $submission->submitted_by
            : ($task->assigned_to ? (int) $task->assigned_to : null);

        $reviewerId = auth()->id() ? (int) auth()->id() : null;

        $titles = [
            'approved'           => 'Task Approved',
            'rejected'           => 'Task Rejected',
            'revision_requested' => 'Revision Requested',
            'completed'          => 'Task Completed',
        ];

        $titleBase    = $titles[$outcome] ?? 'Task Reviewed';
        $humanOutcome = ucfirst(str_replace('_', ' ', $outcome));

        if ($submitterId && $submitterId !== $reviewerId) {
            self::service()->notify(
                $submitterId,
                'task_reviewed',
                $titleBase . ': ' . $task->title,
                'Status: ' . $humanOutcome,
                [
                    'link'          => route('tasks.submit', $task),
                    'task_id'       => $task->id,
                    'submission_id' => $submission?->id,
                    'outcome'       => $outcome,
                ]
            );
        }

        if ($reviewerId) {
            self::service()->notify(
                $reviewerId,
                'task_reviewed',
                'You reviewed: ' . $task->title,
                'Outcome: ' . $humanOutcome,
                [
                    'link'          => route('tasks.show', $task),
                    'task_id'       => $task->id,
                    'submission_id' => $submission?->id,
                    'outcome'       => $outcome,
                ]
            );
        }
    }

    public static function commentAdded(TaskSubmissionComment $comment, TaskSubmission $submission): void
    {
        $task        = $submission->task;
        $commenterId = (int) $comment->user_id;

        $otherPartyId = ($submission->submitted_by === $commenterId)
            ? optional($task->parent)->assigned_to
            : $submission->submitted_by;
        $otherPartyId = $otherPartyId ? (int) $otherPartyId : null;

        $commenterName = $comment->user->name ?? 'Someone';

        if ($otherPartyId && $otherPartyId !== $commenterId) {
            self::service()->notify(
                $otherPartyId,
                'comment_added',
                'New Comment: ' . $task->title,
                $commenterName . ' commented on a submission.',
                [
                    'link'          => route('tasks.submit', $task),
                    'sender_id'     => $commenterId,
                    'task_id'       => $task->id,
                    'submission_id' => $submission->id,
                ]
            );
        }

        self::service()->notify(
            $commenterId,
            'comment_added',
            'Your comment was posted',
            'On: ' . $task->title,
            [
                'link'          => route('tasks.submit', $task),
                'sender_id'     => $commenterId,
                'task_id'       => $task->id,
                'submission_id' => $submission->id,
            ]
        );
    }

    // ==========================================================
    // MAIN TASK LIFECYCLE
    // ==========================================================

    public static function mainTaskCompleted(Task $mainTask): void
    {
        $recipients = self::recipients([
            $mainTask->assigned_to,
            $mainTask->project?->created_by,
            auth()->id(),
        ]);

        foreach ($recipients as $userId) {
            self::service()->notify(
                $userId,
                'task_completed',
                'Main Task Completed: ' . $mainTask->title,
                'All sub-tasks are done. The main task has been marked complete.',
                [
                    'link'       => route('tasks.show', $mainTask),
                    'task_id'    => $mainTask->id,
                    'project_id' => $mainTask->project_id,
                ]
            );
        }
    }

    public static function taskReassigned(Task $task, int $oldAssigneeId, int $newAssigneeId): void
    {
        $actorId = auth()->id() ? (int) auth()->id() : null;

        self::service()->notify(
            $newAssigneeId,
            'task_assigned',
            'You are now leading: ' . $task->title,
            'This task has been reassigned to you.',
            [
                'link'      => route('tasks.show', $task),
                'sender_id' => $actorId,
                'task_id'   => $task->id,
            ]
        );

        self::service()->notify(
            $oldAssigneeId,
            'task_assigned',
            'Task reassigned: ' . $task->title,
            'This task has been reassigned to another member.',
            [
                'link'    => route('tasks.show', $task),
                'task_id' => $task->id,
            ]
        );

        if ($actorId && $actorId !== $newAssigneeId && $actorId !== $oldAssigneeId) {
            self::service()->notify(
                $actorId,
                'task_assigned',
                'You reassigned: ' . $task->title,
                'New leader: ' . ($task->assignee?->name ?? 'someone') . '.',
                [
                    'link'    => route('tasks.show', $task),
                    'task_id' => $task->id,
                ]
            );
        }
    }

    public static function taskUnassigned(Task $task, int $formerAssigneeId): void
    {
        $actorId = auth()->id() ? (int) auth()->id() : null;

        self::service()->notify(
            $formerAssigneeId,
            'task_assigned',
            'Removed from: ' . $task->title,
            'You are no longer assigned to this task.',
            [
                'link'    => route('tasks.show', $task->parent ?? $task),
                'task_id' => $task->id,
            ]
        );

        if ($actorId && $actorId !== $formerAssigneeId) {
            self::service()->notify(
                $actorId,
                'task_assigned',
                'You unassigned: ' . $task->title,
                'The member has been removed from this task.',
                [
                    'link'    => route('tasks.show', $task),
                    'task_id' => $task->id,
                ]
            );
        }
    }

    // ==========================================================
    // PROJECTS
    // ==========================================================

    /**
     * Project created by a Principal.
     *
     * SCOPED notification — ONLY notifies:
     *   - the creator (receipt)
     *   - the project's leader_id
     *   - active project_members
     *
     * Does NOT blanket-notify the whole organization.
     */
    public static function projectCreated($project): void
    {
        $creatorId = $project->created_by ? (int) $project->created_by : null;

        $memberIds = \App\Models\ProjectMember::where('project_id', $project->id)
            ->where('status', 'active')
            ->pluck('user_id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $recipients = self::recipients(array_merge(
            [$creatorId, $project->leader_id],
            $memberIds
        ));

        foreach ($recipients as $userId) {
            $isCreator = ($userId === $creatorId);

            self::service()->notify(
                $userId,
                'project_created',
                $isCreator
                    ? 'Project created: ' . $project->name
                    : 'You were added to: ' . $project->name,
                $isCreator
                    ? 'Your new project is ready.'
                    : 'You have been added to a new project.',
                [
                    'link'       => route('projects.show', $project),
                    'sender_id'  => $creatorId,
                    'project_id' => $project->id,
                ]
            );
        }
    }

    // ==========================================================
    // DEPARTMENTS  (administrative — notify the member only)
    // ==========================================================

    public static function memberAddedToDepartment($user, $department): void
    {
        $userId = $user->id ? (int) $user->id : null;
        if (!$userId) return;

        self::service()->notify(
            $userId,
            'member_joined',
            'Added to ' . $department->name,
            'You have been assigned to the ' . $department->name . ' department.',
            [
                'link'          => route('departments.show', $department),
                'sender_id'     => auth()->id(),
                'department_id' => $department->id,
            ]
        );
    }

    public static function memberRemovedFromDepartment($user, $department): void
    {
        $userId = $user->id ? (int) $user->id : null;
        if (!$userId) return;

        self::service()->notify(
            $userId,
            'system',
            'Removed from ' . $department->name,
            'You are no longer part of the ' . $department->name . ' department.',
            [
                'link'          => route('departments.index'),
                'sender_id'     => auth()->id(),
                'department_id' => $department->id,
            ]
        );
    }
}