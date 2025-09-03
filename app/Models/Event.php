<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'description',
        'type',
        'event_type',
        'status',
        'amount',
        'payment_status',
        'created_date',
        'execution_date',
        'send_date',
        'payment_due_date',
        'completed_at',
        'paid_at',
    ];

    protected $casts = [
        'created_date' => 'datetime',
        'execution_date' => 'datetime',
        'send_date' => 'datetime',
        'payment_due_date' => 'datetime',
        'completed_at' => 'datetime',
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // PERFORMANCE: Suppression des $appends pour éviter les calculs automatiques
    // Les labels et calculs seront ajoutés manuellement quand nécessaire
    protected $appends = [];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('created_date', '>=', now()->subDays($days));
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByEventType($query, string $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    public function scopeStep($query)
    {
        return $query->where('event_type', 'step');
    }

    public function scopeSteps($query)
    {
        return $query->where('event_type', 'step');
    }

    public function scopeTodo($query)
    {
        return $query->where('event_type', 'step')->where('status', 'todo');
    }

    public function scopeDone($query)
    {
        return $query->where('event_type', 'step')->where('status', 'done');
    }

    public function scopeToSend($query)
    {
        return $query->where('event_type', 'billing')->where('status', 'to_send');
    }

    public function scopeSent($query)
    {
        return $query->where('event_type', 'billing')->where('status', 'sent');
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['todo', 'to_send']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['done', 'sent']);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeFuture($query)
    {
        return $query->whereIn('status', ['todo', 'to_send']);
    }

    public function scopePast($query)
    {
        return $query->whereIn('status', ['done', 'sent', 'cancelled']);
    }

    public function scopeBilling($query)
    {
        return $query->where('event_type', 'billing');
    }

    public function scopeTasks($query)
    {
        return $query->whereIn('status', ['todo', 'to_send']);
    }

    public function scopeUpcoming($query)
    {
        return $query->where(function ($q) {
            $q->where(function ($sub) {
                $sub->where('event_type', 'step')
                    ->where('status', 'todo')
                    ->whereDate('execution_date', '>=', now()->toDateString());
            })
                ->orWhere(function ($sub) {
                    $sub->where('event_type', 'billing')
                        ->where('status', 'to_send')
                        ->whereDate('send_date', '>=', now()->toDateString());
                });
        })->orderBy(DB::raw('COALESCE(execution_date, send_date)'), 'asc');
    }

    /**
     * Scope for events with optimized relationships
     */
    public function scopeWithOptimizedRelations($query)
    {
        return $query->with([
            'project:id,name,client_id,status',
            'project.client:id,name,company',
        ]);
    }

    /**
     * Scope for dashboard urgent tasks
     */
    public function scopeUrgentTasks($query, int $limit = 10)
    {
        return $query->with(['project.client:id,name,company'])
            ->where(function ($q) {
                // Overdue step events
                $q->where(function ($sub) {
                    $sub->where('event_type', 'step')
                        ->where('status', 'todo')
                        ->whereDate('execution_date', '<', now());
                })
                // Overdue billing events
                    ->orWhere(function ($sub) {
                        $sub->where('event_type', 'billing')
                            ->where('status', 'to_send')
                            ->whereDate('send_date', '<', now());
                    });
            })
            ->orderByRaw('
                CASE 
                    WHEN event_type = ? THEN execution_date
                    WHEN event_type = ? THEN send_date
                END ASC',
                ['step', 'billing']
            )
            ->limit($limit);
    }

    /**
     * Scope for unpaid invoices with client info
     */
    public function scopeUnpaidInvoices($query)
    {
        return $query->where('event_type', 'billing')
            ->where('payment_status', 'pending')
            ->with([
                'project:id,name,client_id',
                'project.client:id,name,company,email',
            ])
            ->select('id', 'project_id', 'name', 'amount', 'send_date', 'payment_due_date')
            ->orderBy('payment_due_date', 'asc');
    }

    /**
     * Scope for search across events and related models
     */
    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('type', 'like', "%{$term}%")
                ->orWhereHas('project', function ($projectQuery) use ($term) {
                    $projectQuery->where('name', 'like', "%{$term}%")
                        ->orWhereHas('client', function ($clientQuery) use ($term) {
                            $clientQuery->where('name', 'like', "%{$term}%")
                                ->orWhere('company', 'like', "%{$term}%");
                        });
                });
        });
    }

    /**
     * Scope for financial reporting
     */
    public function scopeForFinancialReport($query, $startDate = null, $endDate = null)
    {
        $query = $query->where('event_type', 'billing')
            ->select('id', 'project_id', 'name', 'amount', 'status', 'payment_status', 'send_date', 'paid_at')
            ->with(['project:id,name,client_id', 'project.client:id,name,company']);

        if ($startDate) {
            $query->where('send_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('send_date', '<=', $endDate);
        }

        return $query->orderBy('send_date', 'desc');
    }

    /**
     * Scope for dashboard revenue calculations
     */
    public function scopeRevenueData($query, int $months = 6)
    {
        return $query->where('event_type', 'billing')
            ->where('payment_status', 'paid')
            ->where('paid_at', '>=', now()->subMonths($months))
            ->select('amount', 'paid_at')
            ->orderBy('paid_at');
    }

    public function scopeOverdue($query)
    {
        return $query->where(function ($q) {
            // Étapes en retard : status = todo et execution_date dépassée (avant aujourd'hui)
            $q->where(function ($sub) {
                $sub->where('event_type', 'step')
                    ->where('status', 'todo')
                    ->whereDate('execution_date', '<', now()->toDateString());
            })
            // Facturation en retard : status = to_send et send_date dépassée (avant aujourd'hui)
                ->orWhere(function ($sub) {
                    $sub->where('event_type', 'billing')
                        ->where('status', 'to_send')
                        ->whereDate('send_date', '<', now()->toDateString());
                });
        });
    }

    public function scopePaymentOverdue($query)
    {
        return $query->where('event_type', 'billing')
            ->where('payment_status', 'pending')
            ->whereDate('payment_due_date', '<', now()->toDateString());
    }

    public function scopeUnpaid($query)
    {
        return $query->where('event_type', 'billing')
            ->where('payment_status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('event_type', 'billing')
            ->where('payment_status', 'paid');
    }

    public function getIsTodoAttribute(): bool
    {
        return in_array($this->status, ['todo', 'to_send']);
    }

    public function getIsOverdueAttribute(): bool
    {
        if ($this->event_type === 'step') {
            return $this->status === 'todo' && $this->execution_date && $this->execution_date->startOfDay()->lt(now()->startOfDay());
        } elseif ($this->event_type === 'billing') {
            return $this->status === 'to_send' && $this->send_date && $this->send_date->startOfDay()->lt(now()->startOfDay());
        }

        return false;
    }

    public function getIsPaymentOverdueAttribute(): bool
    {
        return $this->event_type === 'billing' &&
               $this->payment_status === 'pending' &&
               $this->payment_due_date &&
               $this->payment_due_date->startOfDay()->lt(now()->startOfDay());
    }

    public function getDelayDaysAttribute(): ?int
    {
        if (! $this->completed_at) {
            return null;
        }

        $referenceDate = null;
        if ($this->event_type === 'step' && $this->execution_date) {
            $referenceDate = $this->execution_date;
        } elseif ($this->event_type === 'billing' && $this->send_date) {
            $referenceDate = $this->send_date;
        }

        if (! $referenceDate) {
            return null;
        }

        return $this->completed_at->diffInDays($referenceDate, false);
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'todo' => 'À faire',
            'done' => 'Fait',
            'to_send' => 'À envoyer',
            'sent' => 'Envoyé',
            'cancelled' => 'Annulé',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getEventTypeLabelAttribute(): string
    {
        $labels = [
            'step' => 'Étape',
            'billing' => 'Facturation',
        ];

        return $labels[$this->event_type] ?? $this->event_type;
    }

    public function getPaymentStatusLabelAttribute(): ?string
    {
        if ($this->event_type !== 'billing') {
            return null;
        }
        $labels = [
            'paid' => 'Payé',
            'pending' => 'En attente',
        ];

        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    public function getFormattedAmountAttribute(): ?string
    {
        if ($this->event_type !== 'billing' || ! $this->amount) {
            return null;
        }

        return number_format($this->amount, 2, ',', ' ').' €';
    }

    protected static function boot()
    {
        parent::boot();

        // Auto-set completed_at when status changes to done/sent
        static::updating(function ($event) {
            if ($event->isDirty('status')) {
                if (($event->event_type === 'step' && $event->status === 'done') ||
                    ($event->event_type === 'billing' && $event->status === 'sent')) {
                    $event->completed_at = now();
                }
            }
        });
    }
}
