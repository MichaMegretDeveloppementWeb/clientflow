<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Client;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    protected $model = Project::class;

    /**
     * Project name templates in French
     */
    private array $projectTemplates = [
        'Refonte site web {company}',
        'Application mobile {company}',
        'Développement plateforme {sector}',
        'Migration système {company}',
        'Intégration API {service}',
        'Audit sécurité {company}',
        'Formation équipe {company}',
        'Optimisation performance {platform}',
        'Mise en place CRM {company}',
        'Développement module {feature}',
        'Création dashboard {sector}',
        'Automatisation processus {department}',
    ];

    private array $services = [
        'e-commerce', 'gestion', 'comptabilité', 'RH', 'logistique',
        'marketing', 'vente', 'support', 'analytics', 'reporting'
    ];

    private array $features = [
        'paiement', 'authentification', 'notification', 'export',
        'synchronisation', 'facturation', 'inventory', 'planning'
    ];

    private array $platforms = [
        'web', 'mobile', 'desktop', 'cloud', 'hybrid'
    ];

    private array $departments = [
        'commercial', 'technique', 'administratif', 'financier', 'marketing'
    ];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('fr_FR');
        
        // Generate project dates (jamais dans le futur)
        $startDate = $faker->dateTimeBetween('-18 months', 'today');
        
        // Ensure end date is after start date but not in the future
        $minEndDate = (clone $startDate)->modify('+1 day');
        $maxEndDate = min(
            (clone $startDate)->modify('+12 months')->getTimestamp(),
            strtotime('today')
        );
        
        // Si le projet a commencé récemment, il peut ne pas avoir de date de fin
        if ($startDate->getTimestamp() > strtotime('-3 months')) {
            // 60% de chance de ne pas avoir de date de fin pour les projets récents
            if ($faker->boolean(60)) {
                $endDate = null;
            } else {
                $endDate = $faker->dateTimeBetween($minEndDate, date('Y-m-d H:i:s', $maxEndDate));
            }
        } else {
            $endDate = $faker->dateTimeBetween($minEndDate, date('Y-m-d H:i:s', $maxEndDate));
        }
        
        // Determine status based on dates
        $status = $this->determineStatus($startDate, $endDate, $faker);
        
        // Generate budget (realistic ranges)
        $budget = $this->generateBudget($faker);
        
        // Generate created_at that makes sense
        $maxCreatedAt = min($startDate->getTimestamp(), time());
        $createdAt = $faker->dateTimeBetween('-2 years', date('Y-m-d H:i:s', $maxCreatedAt));
        
        // Generate updated_at that is after created_at
        // Use safe date format to avoid DST issues
        $minUpdatedAt = $createdAt->getTimestamp();
        $maxUpdatedAt = time();
        if ($minUpdatedAt < $maxUpdatedAt) {
            // Use safe hours to avoid DST issues (avoiding 2-3 AM)
            $updatedAt = $faker->dateTimeBetween(date('Y-m-d 12:00:00', $minUpdatedAt), date('Y-m-d 12:00:00'));
        } else {
            $updatedAt = new \DateTime(date('Y-m-d 12:00:00', $minUpdatedAt));
        }
        
        return [
            'client_id' => Client::factory(),
            'name' => $this->generateProjectName($faker),
            'description' => $this->generateProjectDescription($faker),
            'status' => $status->value,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'budget' => $budget,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ];
    }

    /**
     * Generate a realistic project name
     */
    private function generateProjectName($faker): string
    {
        $template = $faker->randomElement($this->projectTemplates);
        
        $replacements = [
            '{company}' => $faker->lastName(),
            '{sector}' => $faker->randomElement(['retail', 'finance', 'santé', 'éducation']),
            '{service}' => $faker->randomElement($this->services),
            '{feature}' => $faker->randomElement($this->features),
            '{platform}' => $faker->randomElement($this->platforms),
            '{department}' => $faker->randomElement($this->departments),
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Generate a realistic project description
     */
    private function generateProjectDescription($faker): string
    {
        $objectives = [
            "Moderniser l'infrastructure existante",
            "Améliorer l'expérience utilisateur",
            "Augmenter les performances du système",
            "Automatiser les processus métier",
            "Renforcer la sécurité des données",
            "Faciliter la collaboration interne",
            "Optimiser les coûts opérationnels",
            "Développer de nouvelles fonctionnalités",
        ];
        
        $deliverables = [
            "analyse des besoins",
            "conception technique",
            "développement",
            "tests et validation",
            "déploiement",
            "formation des utilisateurs",
            "documentation technique",
            "support post-livraison",
        ];
        
        $objective = $faker->randomElement($objectives);
        $selectedDeliverables = $faker->randomElements($deliverables, $faker->numberBetween(3, 5));
        
        $description = "Projet visant à {$objective}.\n\n";
        $description .= "Livrables principaux :\n";
        foreach ($selectedDeliverables as $deliverable) {
            $description .= "- " . ucfirst($deliverable) . "\n";
        }
        
        if ($faker->boolean(60)) {
            $description .= "\n" . $faker->text(150);
        }
        
        return $description;
    }

    /**
     * Determine project status based on dates
     */
    private function determineStatus(\DateTime $startDate, ?\DateTime $endDate, $faker): ProjectStatus
    {
        $now = new \DateTime();
        
        // Les projets ne commencent jamais dans le futur
        // Si pas de date de fin, le projet est actif
        if ($endDate === null) {
            return ProjectStatus::Active;
        }
        
        // If project has ended
        if ($endDate < $now) {
            // Most ended projects are completed
            if ($faker->boolean(75)) {
                return ProjectStatus::Completed;
            }
            // Some are cancelled
            if ($faker->boolean(30)) {
                return ProjectStatus::Cancelled;
            }
            // Others might still be active (overrun)
            return ProjectStatus::Active;
        }
        
        // Project is in progress
        if ($faker->boolean(85)) {
            return ProjectStatus::Active;
        }
        
        return $faker->randomElement([
            ProjectStatus::OnHold,
            ProjectStatus::Active,
        ]);
    }

    /**
     * Generate realistic budget amounts
     */
    private function generateBudget($faker): float
    {
        $ranges = [
            [1000, 5000],      // Small projects
            [5000, 15000],     // Medium projects
            [15000, 50000],    // Large projects
            [50000, 150000],   // Enterprise projects
        ];
        
        $range = $faker->randomElement($ranges);
        $budget = $faker->randomFloat(2, $range[0], $range[1]);
        
        // Round to nice numbers for larger budgets
        if ($budget > 10000) {
            $budget = round($budget / 1000) * 1000;
        } elseif ($budget > 1000) {
            $budget = round($budget / 100) * 100;
        }
        
        return $budget;
    }

    /**
     * Indicate that the project is active
     */
    public function active(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProjectStatus::Active->value,
                'start_date' => now()->subMonths(rand(1, 6)),
                'end_date' => now()->addMonths(rand(1, 6)),
            ];
        });
    }

    /**
     * Indicate that the project is completed
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            $endDate = $faker->dateTimeBetween('-6 months', '-1 week');
            
            return [
                'status' => ProjectStatus::Completed->value,
                'start_date' => $faker->dateTimeBetween('-18 months', $endDate),
                'end_date' => $endDate,
            ];
        });
    }

    /**
     * Indicate that the project is on hold
     */
    public function onHold(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProjectStatus::OnHold->value,
            ];
        });
    }

    /**
     * Indicate that the project is cancelled
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => ProjectStatus::Cancelled->value,
            ];
        });
    }

    /**
     * Indicate that the project has a small budget
     */
    public function smallBudget(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            return [
                'budget' => $faker->randomFloat(2, 500, 5000),
            ];
        });
    }

    /**
     * Indicate that the project has a large budget
     */
    public function largeBudget(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            return [
                'budget' => $faker->randomFloat(2, 50000, 200000),
            ];
        });
    }
}