<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // PERFORMANCE: Accesseurs de comptage supprimés également
    // Les withCount() dans les Repository fournissent toujours ces données

    // Optimized relationships
    public function latest_project()
    {
        return $this->hasOne(Project::class)->latest('updated_at');
    }

    // Scopes for optimized queries
    public function scopeActive($query)
    {
        return $query->whereHas('projects', function ($q) {
            $q->where('status', 'active');
        });
    }

    /**
     * Scope for clients with optimized relationships
     */
    public function scopeWithOptimizedRelations($query)
    {
        return $query->with([
            'latest_project' => function ($query) {
                $query->select('id', 'client_id', 'name', 'status', 'budget', 'created_at', 'updated_at');
            },
        ])
            ->withCount([
                'projects as projects_count',
                'projects as active_projects_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'projects as completed_projects_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->addSelect([
                'total_revenue' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->join('projects', 'events.project_id', '=', 'projects.id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('events.event_type', 'billing')
                    ->where('events.payment_status', 'paid'),
                'pending_amount' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->join('projects', 'events.project_id', '=', 'projects.id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('events.event_type', 'billing')
                    ->where('events.payment_status', 'pending'),
                'has_overdue_payments' => Event::select(\DB::raw('COUNT(*) > 0'))
                    ->join('projects', 'events.project_id', '=', 'projects.id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('events.event_type', 'billing')
                    ->where('events.payment_status', 'pending')
                    ->where('events.payment_due_date', '<', \DB::raw('NOW()')),
            ]);
    }

    /**
     * Scope for list pagination (optimized for performance)
     */
    public function scopeForList($query)
    {
        return $query->where('clients.user_id', auth()->id())
            ->select([
                'clients.id',
                'clients.name',
                'clients.company',
                'clients.email',
                'clients.phone',
                'clients.address',
                'clients.notes',
                'clients.created_at',
                'clients.updated_at',
            ])
            ->with([
                'latest_project' => function ($query) {
                    $query->select('id', 'client_id', 'name', 'status', 'budget', 'created_at', 'updated_at');
                },
            ])
            ->withCount([
                'projects as projects_count',
                'projects as active_projects_count' => function ($query) {
                    $query->where('status', 'active');
                },
                'projects as completed_projects_count' => function ($query) {
                    $query->where('status', 'completed');
                },
            ])
            ->addSelect([
                'total_revenue' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->join('projects', 'events.project_id', '=', 'projects.id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('events.event_type', 'billing')
                    ->where('events.payment_status', 'paid'),
                'pending_amount' => Event::select(\DB::raw('COALESCE(SUM(amount), 0)'))
                    ->join('projects', 'events.project_id', '=', 'projects.id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('events.event_type', 'billing')
                    ->where('events.payment_status', 'pending'),
                'has_overdue_payments' => Event::select(\DB::raw('COUNT(*) > 0'))
                    ->join('projects', 'events.project_id', '=', 'projects.id')
                    ->whereColumn('projects.client_id', 'clients.id')
                    ->where('events.event_type', 'billing')
                    ->where('events.payment_status', 'pending')
                    ->where('events.payment_due_date', '<', \DB::raw('NOW()')),
            ]);
    }

    /**
     * Scope for dashboard statistics
     */
    public function scopeForDashboard($query)
    {
        return $query->select('id', 'name', 'company', 'created_at')
            ->withCount([
                'projects as active_projects_count' => function ($query) {
                    $query->where('status', 'active');
                },
            ]);
    }

    /**
     * Scope for search with optimizations
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%")
                ->orWhere('company', 'like', "%{$term}%")
                ->orWhere('phone', 'like', "%{$term}%");
        });
    }

    /**
     * Scope for clients with unpaid invoices
     */
    public function scopeWithUnpaidInvoices($query)
    {
        return $query->whereHas('projects.events', function ($q) {
            $q->where('event_type', 'billing')
                ->where('payment_status', 'pending');
        });
    }

    // PERFORMANCE: TOUS LES ACCESSEURS FINANCIERS SUPPRIMÉS
    // Ces données sont maintenant TOUJOURS calculées dans les Repository via des sous-requêtes
    // pour éviter les 90+ requêtes en double causées par les fallbacks
}
