<?php

namespace App\Console\Commands;

use App\Services\Cache\CacheService;
use Illuminate\Console\Command;

class WarmCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'cache:warm 
                            {--force : Force cache warming even if cache exists}
                            {--stats : Show cache statistics after warming}';

    /**
     * The console command description.
     */
    protected $description = 'Warm up application cache with critical data';

    private CacheService $cacheService;

    /**
     * Create a new command instance.
     */
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting cache warm-up process...');
        $startTime = microtime(true);

        if ($this->option('force')) {
            $this->warn('Forcing cache refresh...');
            \Illuminate\Support\Facades\Cache::flush();
        }

        try {
            $this->warmUpCaches();
            
            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);
            
            $this->info("Cache warm-up completed in {$duration} seconds!");
            
            if ($this->option('stats')) {
                $this->displayCacheStats();
            }
            
            return self::SUCCESS;
            
        } catch (\Exception $e) {
            $this->error('Cache warm-up failed: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Warm up all critical caches
     */
    private function warmUpCaches(): void
    {
        $tasks = [
            'Dashboard Statistics' => fn() => $this->cacheService->getDashboardStats(),
            'Revenue Chart (6 months)' => fn() => $this->cacheService->getRevenueChartData(6),
            'Revenue Chart (12 months)' => fn() => $this->cacheService->getRevenueChartData(12),
            'Clients List' => fn() => $this->cacheService->getClientsList(['limit' => 100]),
            'Projects List' => fn() => $this->cacheService->getProjectsList(['limit' => 100]),
            'Financial Report' => fn() => $this->cacheService->getFinancialReport(),
        ];

        $progressBar = $this->output->createProgressBar(count($tasks));
        $progressBar->setFormat('verbose');

        foreach ($tasks as $taskName => $task) {
            $this->line("  Warming: {$taskName}");
            
            $taskStart = microtime(true);
            $task();
            $taskEnd = microtime(true);
            
            $duration = round(($taskEnd - $taskStart) * 1000, 2);
            $this->line("  ✓ {$taskName} ({$duration}ms)");
            
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Warm up individual client and project caches
        $this->warmUpIndividualCaches();
    }

    /**
     * Warm up individual model caches
     */
    private function warmUpIndividualCaches(): void
    {
        // Warm up top 10 clients by revenue
        $topClients = \App\Models\Client::select('id', 'name')
            ->withOptimizedRelations()
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        $this->line("Warming individual client caches ({$topClients->count()} clients)...");
        
        foreach ($topClients as $client) {
            $this->cacheService->getClientFinancials($client->id);
        }

        // Warm up active projects
        $activeProjects = \App\Models\Project::select('id', 'name')
            ->active()
            ->limit(20)
            ->get();

        $this->line("Warming project metrics caches ({$activeProjects->count()} projects)...");
        
        foreach ($activeProjects as $project) {
            $this->cacheService->getProjectMetrics($project->id);
        }

        $this->info('Individual caches warmed successfully!');
    }

    /**
     * Display cache statistics
     */
    private function displayCacheStats(): void
    {
        $this->newLine();
        $this->info('Cache Statistics:');
        $this->line('================');

        $stats = $this->cacheService->getCacheStats();

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Keys', $stats['total_keys']],
                ['Memory Usage', $stats['memory_usage']],
                ['Dashboard Keys', $stats['keys_by_type']['dashboard']],
                ['Client Keys', $stats['keys_by_type']['clients']],
                ['Project Keys', $stats['keys_by_type']['projects']],
                ['Event Keys', $stats['keys_by_type']['events']],
                ['Revenue Keys', $stats['keys_by_type']['revenue']],
                ['Search Keys', $stats['keys_by_type']['search']],
            ]
        );

        if (isset($stats['error'])) {
            $this->warn($stats['error']);
        }
    }
}