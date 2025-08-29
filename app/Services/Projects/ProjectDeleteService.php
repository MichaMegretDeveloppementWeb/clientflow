<?php

namespace App\Services\Projects;

use App\Models\Project;
use App\Models\Event;
use App\Enums\EventStatus;
use App\Repositories\Contracts\Projects\ProjectDeleteRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProjectDeleteService
{
    public function __construct(
        private readonly ProjectDeleteRepositoryInterface $projectDeleteRepository
    ) {}

    /**
     * Delete project (soft delete recommended)
     */
    public function deleteProject(Project $project): bool
    {
        return DB::transaction(function () use ($project) {
            // Cancel all pending events
            Event::where('project_id', $project->id)
                ->whereIn('status', [EventStatus::Todo->value, EventStatus::ToSend->value])
                ->update(['status' => EventStatus::Cancelled->value]);

            // Log activity
            $this->logProjectActivity($project, 'deleted');

            return $this->projectDeleteRepository->delete($project);
        });
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
