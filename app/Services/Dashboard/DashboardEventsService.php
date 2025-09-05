<?php

namespace App\Services\Dashboard;

use App\Repositories\Contracts\Dashboard\TasksRepositoryInterface;
use Illuminate\Support\Collection;

class DashboardEventsService
{
    public function __construct(
        private readonly TasksRepositoryInterface $tasksRepository
    ) {}

    /**
     * Get upcoming tasks with optional urgency filter
     * OPTIMIZED: Repository pattern + error handling
     */
    public function getUpcomingTasks(bool $urgentOnly = false, int $limit = 100): Collection
    {
        try {
            $userId = auth()->id();

            return $this->tasksRepository->getUpcomingTasks($userId, $urgentOnly);
        } catch (\Exception $e) {
            // Log error for debugging but return empty collection
            \Log::error('Error fetching upcoming tasks: '.$e->getMessage());

            return collect([]);
        }
    }

    /**
     * Get urgent tasks count
     */
    public function getUrgentTasksCount(): int
    {
        try {
            $userId = auth()->id();

            return $this->tasksRepository->getUrgentTasksCount($userId);
        } catch (\Exception $e) {
            \Log::error('Error fetching urgent tasks count: '.$e->getMessage());

            return 0;
        }
    }

    /**
     * Get urgent tasks (overdue events) - kept for backward compatibility
     */
    public function getUrgentTasks(int $limit = 10): Collection
    {
        return $this->getUpcomingTasks(true, $limit);
    }
}
