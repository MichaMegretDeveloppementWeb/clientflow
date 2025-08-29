<?php

namespace App\Repositories\Clients;

use App\Models\Client;
use App\Enums\ProjectStatus;
use App\Enums\PaymentStatus;
use App\Repositories\Contracts\Clients\ClientListRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class ClientListRepository implements ClientListRepositoryInterface
{
    /**
     * Get paginated clients
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Client::forList();
        
        // Apply search filter
        if (!empty($filters['search'])) {
            $searchTerm = '%' . $filters['search'] . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('company', 'like', $searchTerm)
                  ->orWhere('email', 'like', $searchTerm)
                  ->orWhere('phone', 'like', $searchTerm);
            });
        }
        
        // Filter by has projects
        if (isset($filters['has_projects']) && $filters['has_projects'] !== null) {
            if ($filters['has_projects'] === 'true') {
                $query->whereHas('projects');
            } elseif ($filters['has_projects'] === 'false') {
                $query->whereDoesntHave('projects');
            }
        }
        
        // Filter by has active projects
        if (!empty($filters['has_active_projects']) && $filters['has_active_projects'] === 'true') {
            $query->whereHas('projects', function ($q) {
                $q->where('status', ProjectStatus::Active->value);
            });
        }
        
        // Filter by has unpaid invoices
        if (!empty($filters['has_unpaid_invoices'])) {
            $query->whereHas('projects.events', function ($q) {
                $q->where('event_type', 'billing')
                  ->where('payment_status', PaymentStatus::Pending->value);
            });
        }
        
        // Filter by has overdue payments
        if (isset($filters['has_overdue_payments']) && 
            ($filters['has_overdue_payments'] === true || $filters['has_overdue_payments'] === 'true' || $filters['has_overdue_payments'] === 'on')) {
            $query->whereHas('projects.events', function ($q) {
                $q->where('event_type', 'billing')
                  ->where('payment_status', PaymentStatus::Pending->value)
                  ->where('payment_due_date', '<', now());
            });
        }
        
        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        
        // Pour les champs calculés, utiliser orderBy directement
        // car ils sont déjà ajoutés via addSelect() dans le scope forList()
        if (in_array($sortBy, ['projects_count', 'active_projects_count', 'completed_projects_count', 'total_revenue', 'pending_amount', 'has_overdue_payments'])) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            // Pour les champs normaux, prefixer avec la table
            $query->orderBy("clients.{$sortBy}", $sortOrder);
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Get global client list statistics
     */
    public function getGlobalStatistics(): array
    {
        // Statistiques globales pour les clients de l'utilisateur connecté seulement
        $totalClients = Client::where('user_id', auth()->id())->count();
        
        // Clients avec au moins un projet
        $clientsWithProjects = Client::where('user_id', auth()->id())
            ->whereHas('projects')->count();
        
        // Clients sans projets
        $clientsWithoutProjects = $totalClients - $clientsWithProjects;
        
        // Clients avec projets actifs
        $clientsWithActiveProjects = Client::where('user_id', auth()->id())
            ->whereHas('projects', function ($query) {
                $query->where('status', ProjectStatus::Active->value);
            })->count();
        
        return [
            'total' => $totalClients,
            'with_projects' => $clientsWithProjects,
            'without_projects' => $clientsWithoutProjects,
            'with_active_projects' => $clientsWithActiveProjects,
        ];
    }
}