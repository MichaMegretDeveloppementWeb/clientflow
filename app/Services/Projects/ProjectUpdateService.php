<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\Event;
use App\Enums\ProjectStatus;
use App\Enums\EventType;
use App\Enums\EventStatus;
use App\Repositories\Contracts\Projects\ProjectUpdateRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProjectUpdateService
{
    public function __construct(
        private readonly ProjectUpdateRepositoryInterface $projectRepository,
    ) {}

    /**
     * Update existing project
     */
    public function updateProject(Project $project, array $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $oldStatus = $project->status;

            $updatedProject = $this->projectRepository->update($project, $data);

            // Check if status changed
            if (isset($data['status']) && $oldStatus !== $data['status']) {
                $this->handleStatusChange($updatedProject, $oldStatus, $data['status']);
            }

            // Log activity
            $this->logProjectActivity($updatedProject, 'updated');

            return $updatedProject;
        });
    }

    /**
     * Handle project status change
     */
    private function handleStatusChange(Project $project, string $oldStatus, string $newStatus): void
    {
        // When project is completed, mark all pending tasks as cancelled
        if ($newStatus === ProjectStatus::Completed->value) {
            Event::where('project_id', $project->id)
                ->where('event_type', EventType::Step->value)
                ->where('status', EventStatus::Todo->value)
                ->update(['status' => EventStatus::Cancelled->value]);
        }

        // When project is reactivated, you might want to review cancelled tasks
        if ($oldStatus === ProjectStatus::Completed->value && $newStatus === ProjectStatus::Active->value) {
            // Notify user to review cancelled tasks
        }
    }

    /**
     * Log project activity (for audit trail)
     */
    private function logProjectActivity(Project $project, string $action): void
    {
        \Log::info("Project {$action}", [
            'project_id' => $project->id,
            'project_name' => $project->name,
            'user_id' => auth()->id(),
            'action' => $action,
            'timestamp' => now(),
        ]);
    }
}
