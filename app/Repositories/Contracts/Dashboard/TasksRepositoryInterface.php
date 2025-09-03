<?php

namespace App\Repositories\Contracts\Dashboard;

use Illuminate\Support\Collection;

interface TasksRepositoryInterface
{
    /**
     * Get upcoming tasks with optional urgency filter
     */
    public function getUpcomingTasks(int $userId, bool $urgentOnly = false, int $limit = 100): Collection;

    /**
     * Get urgent tasks count
     */
    public function getUrgentTasksCount(int $userId): int;
}
