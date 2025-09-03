<?php

namespace App\Http\Controllers;

use App\Http\Resources\Dashboard\ActivityResource;
use App\Http\Resources\Dashboard\BillingResource;
use App\Http\Resources\Dashboard\HelpResource;
use App\Http\Resources\Dashboard\QuickStatsResource;
use App\Http\Resources\Dashboard\RevenueResource;
use App\Http\Resources\Dashboard\StatisticsResource;
use App\Http\Resources\Dashboard\TaskResource;
use App\Services\Dashboard\DashboardActivitiesService;
use App\Services\Dashboard\DashboardBillingService;
use App\Services\Dashboard\DashboardEventsService;
use App\Services\Dashboard\DashboardHelpService;
use App\Services\Dashboard\DashboardQuickStatsService;
use App\Services\Dashboard\DashboardRevenueService;
use App\Services\Dashboard\DashboardStatisticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardEventsService $eventsService,
        private readonly DashboardActivitiesService $activitiesService,
        private readonly DashboardRevenueService $revenueService,
        private readonly DashboardStatisticsService $statisticsService,
        private readonly DashboardBillingService $billingService,
        private readonly DashboardQuickStatsService $quickStatsService,
        private readonly DashboardHelpService $helpService
    ) {}

    public function index(): Response
    {
        return Inertia::render('Dashboard/Index');
    }

    public function urgentTasks(Request $request): JsonResponse
    {
        $urgentOnly = $request->boolean('urgent_only', false);

        // Le service gère les erreurs et retourne une collection (vide si erreur)
        $tasks = $this->eventsService->getUpcomingTasks($urgentOnly);

        return TaskResource::collection($tasks)->response()->setData([
            'urgent_tasks' => TaskResource::collection($tasks),
        ]);
    }

    public function recentActivities(): JsonResponse
    {
        // Récupérer les activités récentes spécifiquement
        $activities = $this->activitiesService->getRecentActivities();

        return ActivityResource::collection($activities)->response()->setData([
            'recent_activities' => ActivityResource::collection($activities),
        ]);
    }

    public function statistics(): JsonResponse
    {
        // Récupérer les statistiques de base pour StatsGrid
        $statistics = $this->statisticsService->getBasicStatistics();

        return (new StatisticsResource($statistics))->response()->setData([
            'statistics' => new StatisticsResource($statistics),
        ]);
    }

    public function billing(): JsonResponse
    {
        // Le service gère le try-catch et retourne soit les données soit une structure d'erreur
        $billingData = $this->billingService->getBillingCardsData();

        return (new BillingResource($billingData))->response()->setData([
            'billing' => new BillingResource($billingData),
            'error' => $billingData['error'] ?? null,
        ]);
    }

    public function revenueChart(Request $request): JsonResponse
    {
        $period = $request->get('period', 'current_month');

        // Le service gère le try-catch et retourne soit les données soit une structure d'erreur
        $chartData = $this->revenueService->getRevenueChartData($period);

        return (new RevenueResource($chartData))->response()->setData([
            'revenue_chart' => new RevenueResource($chartData),
            'error' => $chartData['error'] ?? null,
        ]);
    }

    public function quickStats(): JsonResponse
    {
        // Le service gère le try-catch et retourne soit les données soit une structure d'erreur
        $quickStats = $this->quickStatsService->getQuickStats();

        return (new QuickStatsResource($quickStats))->response()->setData([
            'quick_stats' => new QuickStatsResource($quickStats),
            'error' => $quickStats['error'] ?? null,
        ]);
    }

    public function help(): JsonResponse
    {
        // Récupérer les données d'aide et d'onboarding
        $helpData = $this->helpService->getHelpData();

        return (new HelpResource($helpData))->response()->setData([
            'help' => new HelpResource($helpData),
        ]);
    }
}
