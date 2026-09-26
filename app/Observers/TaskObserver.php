<?php
// app/Observers/TaskObserver.php

namespace App\Observers;

use App\Helpers\NotificationHelper;
use App\Models\Task;

class TaskObserver
{
    /**
     * When a sub-task is created, if the parent is still "todo", move it to "in_progress".
     */
    public function created(Task $task): void
    {
        if ($task->isSub() && $task->parent) {
            $parent = $task->parent;
            if ($parent->status === Task::STATUS_TODO) {
                $parent->status = Task::STATUS_IN_PROGRESS;
                $parent->saveQuietly();
            }
        }
    }

    /**
     * When a sub-task's status changes, recompute the parent.
     * When the parent transitions into "done", fire the completion notification
     * and unblock any dependent tasks.
     */
    public function updated(Task $task): void
    {
        // Sub-task status changed → recompute parent
        if ($task->isSub() && $task->wasChanged('status')) {
            $this->syncParent($task);
        }

        // Main task manually set to "done" while subs unfinished → force back
        if ($task->isMain() && $task->wasChanged('status') && $task->status === Task::STATUS_DONE) {
            if ($task->subtasks()->where('status', '!=', Task::STATUS_DONE)->exists()) {
                $task->status = Task::STATUS_IN_PROGRESS;
                $task->saveQuietly();
            } else {
                $task->completed_at = $task->completed_at ?: now();
                $task->saveQuietly();

                // Manually-completed main task → unblock dependents too
                $this->unblockDependents($task);
            }
        }
    }

    /**
     * When a sub-task is deleted, recompute the parent.
     */
    public function deleted(Task $task): void
    {
        if ($task->isSub() && $task->parent) {
            $this->syncParent($task);
        }
    }

    /**
     * Recompute a parent main task's status from its sub-tasks.
     * Fires the main-task-completed notification and unblocks dependents
     * on the transition into "done".
     */
    protected function syncParent(Task $subtask): void
    {
        $parent = $subtask->parent;
        if (!$parent) return;

        $previous  = $parent->status;
        $newStatus = $parent->computeStatusFromSubtasks();

        if ($previous === $newStatus) return;

        $parent->status       = $newStatus;
        $parent->completed_at = $newStatus === Task::STATUS_DONE ? now() : null;
        $parent->saveQuietly();

        // On the transition INTO "done":
        //  - notify the leader + project creator
        //  - unblock any main tasks that were waiting on this one
        if ($previous !== Task::STATUS_DONE && $newStatus === Task::STATUS_DONE) {
            NotificationHelper::mainTaskCompleted($parent);
            $this->unblockDependents($parent);
        }
    }

    /**
     * When a task completes, flip `is_blocked` back to false on any
     * tasks that depended on it — but only if all their other
     * dependencies are also done.
     */
    protected function unblockDependents(Task $completedTask): void
    {
        $dependents = Task::whereHas('dependsOnTasks', function ($q) use ($completedTask) {
            $q->where('depends_on_task_id', $completedTask->id);
        })->get();

        foreach ($dependents as $dependent) {
            $stillBlocked = $dependent->dependsOnTasks()
                ->where('status', '!=', Task::STATUS_DONE)
                ->exists();

            if (!$stillBlocked && $dependent->is_blocked) {
                $dependent->is_blocked = false;
                $dependent->saveQuietly();
            }
        }
    }
}