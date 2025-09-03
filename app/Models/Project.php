<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'name',
        'description',
        'status',
        'start_date',
        'end_date',
        'budget',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'budget' => 'decimal:2',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class)->orderBy('created_at', 'desc');
    }

    // Optimized relationships
    public function latest_event(): HasOne
    {
        return $this->hasOne(Event::class)->latest('created_at');
    }

    // PERFORMANCE: Accesseurs supprimés pour éviter les requêtes automatiques
    // Les données sont maintenant calculées dans les Repository avec des sous-requêtes

    // Optimized scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'active')
            ->where('end_date', '<', now());
    }

    /**
     * Scope for projects with optimized relationships
     */
    public function scopeWithOptimizedRelations($query)
    {
        return $query->with([
            'client:id,name,company,email',
            'latest_event:id,project_id,name,event_type,status,amount,created_at',
        ])
            ->withCount([
                'events as events_count',
                'events as completed_tasks_count' => function ($query) {
                    $query->where('event_type', 'step')
                        ->where('status', 'done');
                },
                'events as pending_tasks_count' => function ($query) {
                    $query->where('event_type', 'step')
                        ->where('status', 'todo');
                },
            ])
            ->addSelect([
                'total_billed' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->whereNotIn('status', ['cancelled']),
                'total_paid' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'paid'),
                'total_unpaid' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'pending'),
                'has_overdue_events' => Event::select(\DB::raw('COUNT(*) > 0'))
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
                'has_payment_overdue' => Event::select(\DB::raw('COUNT(*) > 0'))
                    ->whereColumn('project_id', 'projects.id')
                    ->where('event_type', 'billing')
                    ->where('payment_status', 'pending')
                    ->whereDate('payment_due_date', '<', now()->toDateString()),
            ]);
    }

    /**
     * Scope for dashboard statistics
     */
    public function scopeForDashboard($query)
    {
        return $query->select('id', 'name', 'client_id', 'status', 'end_date', 'created_at')
            ->with('client:id,name,company');
    }

    /**
     * Scope for search with client information
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhereHas('client', function ($clientQuery) use ($term) {
                    $clientQuery->where('name', 'like', "%{$term}%")
                        ->orWhere('company', 'like', "%{$term}%");
                });
        });
    }

    /**
     * Scope for projects with budget exceeded
     */
    public function scopeBudgetExceeded($query)
    {
        return $query->whereRaw('
            budget > 0 AND (
                SELECT COALESCE(SUM(amount), 0) 
                FROM events 
                WHERE project_id = projects.id 
                AND event_type = "billing" 
                AND status != "cancelled"
            ) > budget
        ');
    }

    /**
     * Scope for projects with unpaid invoices
     */
    public function scopeWithUnpaidInvoices($query)
    {
        return $query->whereHas('events', function ($q) {
            $q->where('event_type', 'billing')
                ->where('payment_status', 'pending');
        });
    }

    // PERFORMANCE: TOUS LES ACCESSEURS FINANCIERS SUPPRIMÉS
    // Ces calculs sont maintenant fait via des sous-requêtes dans les Repository
    // pour éviter les 300+ requêtes automatiques

    /**
     * Retourne les tâches à venir pour ce projet
     */
    public function upcomingTasks()
    {
        return $this->events()
            ->whereIn('status', ['todo', 'to_send'])
            ->orderBy(\DB::raw('COALESCE(execution_date, send_date)'), 'asc');
    }

    /**
     * Retourne les tâches en retard pour ce projet
     */
    public function overdueTasks()
    {
        return $this->events()->overdue();
    }

    // PERFORMANCE: TOUS LES ACCESSEURS DE VÉRIFICATION SUPPRIMÉS
    // Ces vérifications sont maintenant faites via des sous-requêtes dans les Repository
}
