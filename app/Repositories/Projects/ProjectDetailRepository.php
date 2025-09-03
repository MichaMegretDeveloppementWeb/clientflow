<?php

namespace App\Repositories\Projects;

use App\Models\Project;
use App\Models\Event;
use App\Repositories\Contracts\Projects\ProjectDetailRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ProjectDetailRepository implements ProjectDetailRepositoryInterface
{
    /**
     * Find project with specific relations
     */
    public function findWithRelations(int $projectId, array $relations = []): ?Project
    {
        return Project::with($relations)
            ->whereRelation('client', 'user_id', auth()->id())
            ->find($projectId);
    }

    /**
     * Find project with complete financial data for detail page
     */
    public function findWithFinancialStats(int $projectId): ?Project
    {
        return Project::select('projects.*')
            ->whereRelation('client', 'user_id', auth()->id())
            ->with([
                'client:id,name,company,email',
                'events' => function ($query) {
                    $query->orderBy('created_date', 'desc');
                }
            ])
            ->addSelect([
                // Montants facturés (toutes les factures non annulées)
                'total_billed' => Event::select(DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->whereNotIn('status', ['cancelled']),

                // Montants payés
                'total_paid' => Event::select(DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'paid'),

                // Montants impayés
                'total_unpaid' => Event::select(DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'pending'),

                // Factures à envoyer (créées mais pas envoyées)
                'bills_to_send' => Event::select(DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('status', 'to_send'),

                // Paiements à venir (envoyées, non payées, échéance future)
                'upcoming_payments' => Event::select(DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('status', 'sent')
                    ->where('payment_status', 'pending')
                    ->whereDate('payment_due_date', '>=', now()->toDateString()),

                // Impayés en retard (envoyées, non payées, échéance dépassée)
                'overdue_unpaid' => Event::select(DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('status', 'sent')
                    ->where('payment_status', 'pending')
                    ->whereDate('payment_due_date', '<', now()->toDateString()),

                // Indicateurs booléens
                'has_overdue_events' => Event::select(DB::raw('COUNT(*) > 0'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where(function ($q) {
                        $q->where(function ($sub) {
                            $sub->where('event_type', 'step')
                                ->where('status', 'todo')
                                ->whereDate('execution_date', '<', now()->toDateString());
                        })
                        ->orWhere(function ($sub) {
                            $sub->where('event_type', 'billing')
                                ->where('status', 'to_send')
                                ->whereDate('send_date', '<', now()->toDateString());
                        });
                    }),

                'has_overdue_payments' => Event::select(DB::raw('COUNT(*) > 0'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'pending')
                    ->whereDate('payment_due_date', '<', now()->toDateString()),
            ])
            ->find($projectId);
    }
}
