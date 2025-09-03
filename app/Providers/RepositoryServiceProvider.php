<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register repository bindings
     */
    public function register(): void
    {
        // Bind repository interfaces to implementations
        $this->app->bind(
            \App\Repositories\Contracts\ClientRepositoryInterface::class,
            \App\Repositories\ClientRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientListRepositoryInterface::class,
            \App\Repositories\Clients\ClientListRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientDetailRepositoryInterface::class,
            \App\Repositories\Clients\ClientDetailRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientCreateRepositoryInterface::class,
            \App\Repositories\Clients\ClientCreateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientStoreRepositoryInterface::class,
            \App\Repositories\Clients\ClientStoreRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientEditRepositoryInterface::class,
            \App\Repositories\Clients\ClientEditRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientUpdateRepositoryInterface::class,
            \App\Repositories\Clients\ClientUpdateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Clients\ClientDeleteRepositoryInterface::class,
            \App\Repositories\Clients\ClientDeleteRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ProjectRepositoryInterface::class,
            \App\Repositories\ProjectRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventListRepositoryInterface::class,
            \App\Repositories\Events\EventListRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventDetailRepositoryInterface::class,
            \App\Repositories\Events\EventDetailRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventUpdateRepositoryInterface::class,
            \App\Repositories\Events\EventUpdateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventEditRepositoryInterface::class,
            \App\Repositories\Events\EventEditRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventCreateRepositoryInterface::class,
            \App\Repositories\Events\EventCreateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventStoreRepositoryInterface::class,
            \App\Repositories\Events\EventStoreRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Events\EventDeleteRepositoryInterface::class,
            \App\Repositories\Events\EventDeleteRepository::class
        );

        // Projects repositories bindings
        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectListRepositoryInterface::class,
            \App\Repositories\Projects\ProjectListRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectDetailRepositoryInterface::class,
            \App\Repositories\Projects\ProjectDetailRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectCreateRepositoryInterface::class,
            \App\Repositories\Projects\ProjectCreateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectStoreRepositoryInterface::class,
            \App\Repositories\Projects\ProjectStoreRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectEditRepositoryInterface::class,
            \App\Repositories\Projects\ProjectEditRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectUpdateRepositoryInterface::class,
            \App\Repositories\Projects\ProjectUpdateRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Projects\ProjectDeleteRepositoryInterface::class,
            \App\Repositories\Projects\ProjectDeleteRepository::class
        );

        // Dashboard repositories bindings
        $this->app->bind(
            \App\Repositories\Contracts\Dashboard\StatisticsRepositoryInterface::class,
            \App\Repositories\Dashboard\StatisticsRepository::class
        );

        // Dashboard repositories bindings
        $this->app->bind(
            \App\Repositories\Contracts\Dashboard\RevenueChartRepositoryInterface::class,
            \App\Repositories\Dashboard\RevenueChartRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Dashboard\QuickStatsRepositoryInterface::class,
            \App\Repositories\Dashboard\QuickStatsRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Dashboard\TasksRepositoryInterface::class,
            \App\Repositories\Dashboard\TasksRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Dashboard\ActivitiesRepositoryInterface::class,
            \App\Repositories\Dashboard\ActivitiesRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\Dashboard\BillingRepositoryInterface::class,
            \App\Repositories\Dashboard\BillingRepository::class
        );
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        //
    }
}
