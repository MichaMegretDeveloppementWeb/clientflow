<?php

namespace App\Services\Dashboard;

use App\Repositories\Contracts\Dashboard\BillingRepositoryInterface;

class DashboardBillingService
{
    public function __construct(
        private readonly BillingRepositoryInterface $billingRepository
    ) {}

    /**
     * Get billing cards data for dashboard
     * OPTIMIZED: Repository pattern + error handling
     */
    public function getBillingCardsData(): array
    {
        try {
            $userId = auth()->id();
            $data = $this->billingRepository->getBillingStatistics($userId);

            // Add calculated payment rate
            $data['payment_rate'] = $this->calculatePaymentRate($data['total_sent'], $data['total_paid']);

            return $data;
        } catch (\Exception $e) {
            // Log error and return default values
            \Log::error('Error fetching billing data: '.$e->getMessage());

            return [
                'total_billed' => 0.0,
                'total_to_send' => 0.0,
                'total_sent' => 0.0,
                'total_paid' => 0.0,
                'total_overdue_payment' => 0.0,
                'total_upcoming_payment' => 0.0,
                'payment_rate' => 0.0,
                'invoices_to_send_count' => 0,
                'unpaid_invoices_count' => 0,
                'overdue_invoices_count' => 0,
                'error' => app()->environment('local')
                    ? $e->getMessage().' in '.$e->getFile().':'.$e->getLine()
                    : 'Erreur lors du chargement des données de facturation.',
            ];
        }
    }

    /**
     * Calculate payment rate (paid / billed)
     */
    private function calculatePaymentRate(float $totalSent, float $totalPaid): float
    {
        if ($totalSent == 0) {
            return 0.0;
        }

        return round(($totalPaid / $totalSent) * 100, 1);
    }
}
