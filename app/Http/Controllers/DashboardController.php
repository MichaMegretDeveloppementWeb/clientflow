<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use App\Services\Dashboard\DashboardActivitiesService;
use App\Services\Dashboard\DashboardEventsService;
use App\Services\Dashboard\DashboardService;
use App\Services\Dashboard\DashboardRevenueService;
use App\Services\Dashboard\DashboardStatisticsService;
use App\Services\Dashboard\DashboardBillingService;
use App\Services\Dashboard\DashboardQuickStatsService;
use App\Services\Dashboard\DashboardHelpService;
use App\DTOs\DashboardDTO;
use App\Http\Resources\Dashboard\TaskResource;
use App\Http\Resources\Dashboard\ActivityResource;
use App\Http\Resources\Dashboard\RevenueResource;
use App\Http\Resources\Dashboard\StatisticsResource;
use App\Http\Resources\Dashboard\BillingResource;
use App\Http\Resources\Dashboard\QuickStatsResource;
use App\Http\Resources\Dashboard\HelpResource;
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
        // Récupérer le paramètre pour filtrer les tâches urgentes uniquement
        $urgentOnly = $request->boolean('urgent_only', false);

        // Récupérer les tâches à venir (avec filtre optionnel pour urgentes)
        $tasks = $this->eventsService->getUpcomingTasks($urgentOnly);

        return TaskResource::collection($tasks)->response()->setData([
            'urgent_tasks' => TaskResource::collection($tasks)
        ]);
    }


    public function recentActivities(): JsonResponse
    {
        // Récupérer les activités récentes spécifiquement
        $activities = $this->activitiesService->getRecentActivities();

        return ActivityResource::collection($activities)->response()->setData([
            'recent_activities' => ActivityResource::collection($activities)
        ]);
    }


    public function statistics(): JsonResponse
    {
        // Récupérer les statistiques du dashboard
        $statistics = $this->statisticsService->getStatistics();

        return (new StatisticsResource($statistics))->response()->setData([
            'statistics' => new StatisticsResource($statistics)
        ]);
    }


    public function billing(): JsonResponse
    {
        // Récupérer les données de facturation
        $billingData = $this->billingService->getBillingCardsData();

        return (new BillingResource($billingData))->response()->setData([
            'billing' => new BillingResource($billingData)
        ]);
    }


    public function revenueChart(Request $request): JsonResponse
    {
        $period = $request->get('period', 'current_month');

        // Utiliser le service de revenue pour obtenir les données du graphique
        $chartData = $this->revenueService->getRevenueChartData($period);

        return (new RevenueResource($chartData))->response()->setData([
            'revenue_chart' => new RevenueResource($chartData)
        ]);
    }


    public function quickStats(): JsonResponse
    {
        // Récupérer les statistiques rapides
        $quickStats = $this->quickStatsService->getQuickStats();

        return (new QuickStatsResource($quickStats))->response()->setData([
            'quick_stats' => new QuickStatsResource($quickStats)
        ]);
    }


    public function help(): JsonResponse
    {
        // Récupérer les données d'aide et d'onboarding
        $helpData = $this->helpService->getHelpData();

        return (new HelpResource($helpData))->response()->setData([
            'help' => new HelpResource($helpData)
        ]);
    }

}
