<?php

namespace Database\Factories;

use App\Enums\EventCategory;
use App\Enums\EventStatus;
use App\Enums\EventType;
use App\Enums\PaymentStatus;
use App\Models\Event;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Step event name templates
     */
    private array $stepTemplates = [
        'meeting' => [
            'Réunion de lancement',
            'Point d\'avancement hebdomadaire',
            'Réunion de validation',
            'Présentation des livrables',
            'Réunion de clôture',
        ],
        'development' => [
            'Développement module {module}',
            'Implémentation fonctionnalité {feature}',
            'Intégration API {api}',
            'Correction bugs sprint {number}',
            'Refactoring {component}',
        ],
        'design' => [
            'Création maquettes',
            'Design système',
            'Validation UX/UI',
            'Prototypage interactif',
            'Guide de style',
        ],
        'testing' => [
            'Tests unitaires',
            'Tests d\'intégration',
            'Tests de performance',
            'Recette utilisateur',
            'Tests de sécurité',
        ],
    ];

    /**
     * Billing event name templates
     */
    private array $billingTemplates = [
        'invoice' => [
            'Facture #{number}',
            'Facture mensuelle {month}',
            'Facture finale projet',
            'Facture intermédiaire',
        ],
        'quote' => [
            'Devis #{number}',
            'Devis initial',
            'Devis complémentaire',
        ],
        'deposit' => [
            'Acompte 30%',
            'Acompte 50%',
            'Acompte initial',
        ],
    ];

    private array $modules = [
        'authentification', 'paiement', 'notification', 'reporting',
        'dashboard', 'administration', 'API', 'export'
    ];

    private array $features = [
        'recherche avancée', 'filtres dynamiques', 'export PDF',
        'notifications push', 'mode hors ligne', 'synchronisation',
        'multi-langue', 'dark mode'
    ];

    private array $apis = [
        'Stripe', 'PayPal', 'Google Maps', 'SendGrid',
        'Twilio', 'AWS S3', 'Slack', 'Teams'
    ];

    private array $components = [
        'base de données', 'cache', 'authentification',
        'routing', 'middlewares', 'services'
    ];

    /**
     * Define the model's default state.
     * 
     * IMPORTANT: Cet événement sera lié à un projet existant lors du seeding,
     * les dates doivent être cohérentes avec le projet parent.
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('fr_FR');
        
        // Randomly choose event type
        $eventType = $faker->randomElement([EventType::Step, EventType::Billing]);
        
        // Base attributes common to all events
        $baseAttributes = [
            'project_id' => Project::factory(),
            'event_type' => $eventType->value,
            'created_date' => $faker->dateTimeBetween('-1 year', 'now'),
        ];
        
        // Add type-specific attributes
        if ($eventType === EventType::Step) {
            return array_merge($baseAttributes, $this->stepEventAttributes($faker));
        } else {
            return array_merge($baseAttributes, $this->billingEventAttributes($faker));
        }
    }
    
    /**
     * Create an event for a specific project with coherent dates
     */
    public function forProject(Project $project): static
    {
        return $this->state(function (array $attributes) use ($project) {
            $faker = \Faker\Factory::create('fr_FR');
            $eventType = EventType::from($attributes['event_type'] ?? EventType::Step->value);
            
            // Les événements ne peuvent pas être créés avant le début du projet
            $projectStart = $project->start_date;
            $projectEnd = $project->end_date ?? now();
            
            // Date de création : entre le début du projet et maintenant
            $createdDate = $faker->dateTimeBetween(
                max($projectStart, strtotime('-1 year')),
                'now'
            );
            
            $baseAttributes = [
                'project_id' => $project->id,
                'created_date' => $createdDate,
            ];
            
            if ($eventType === EventType::Step) {
                return array_merge($baseAttributes, $this->stepEventAttributesForProject($faker, $project, $createdDate));
            } else {
                return array_merge($baseAttributes, $this->billingEventAttributesForProject($faker, $project, $createdDate));
            }
        });
    }

    /**
     * Generate attributes for step events
     */
    private function stepEventAttributes($faker): array
    {
        $category = $faker->randomElement(EventCategory::forEventType(EventType::Step));
        $status = $faker->randomElement([EventStatus::Todo, EventStatus::Done, EventStatus::Cancelled]);
        
        // Generate dates based on status
        $createdDate = $faker->dateTimeBetween('-6 months', 'now');
        
        // Ensure execution date is after created date BUT NEVER IN THE FUTURE
        $minExecutionDate = $createdDate->getTimestamp();
        $maxExecutionDate = strtotime('today'); // Jamais dans le futur
        $executionDate = $faker->dateTimeBetween(
            date('Y-m-d H:i:s', $minExecutionDate), 
            date('Y-m-d H:i:s', $maxExecutionDate)
        );
        
        $completedAt = null;
        
        if ($status === EventStatus::Done) {
            // If done, it might have been completed near or after execution date
            $minCompleted = $executionDate->getTimestamp();
            $maxCompleted = min($minCompleted + (7 * 24 * 60 * 60), time());
            if ($minCompleted < $maxCompleted) {
                $completedAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minCompleted), date('Y-m-d H:i:s', $maxCompleted));
            } else {
                $completedAt = new \DateTime(date('Y-m-d H:i:s', $minCompleted));
            }
        } elseif ($status === EventStatus::Cancelled) {
            // If cancelled, it was stopped at some point
            $minCancelled = $createdDate->getTimestamp();
            $maxCancelled = time();
            if ($minCancelled < $maxCancelled) {
                $completedAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minCancelled), 'now');
            } else {
                $completedAt = new \DateTime();
            }
        }
        
        return [
            'name' => $this->generateStepName($faker, $category),
            'description' => $this->generateStepDescription($faker, $category),
            'type' => $category->value,
            'status' => $status->value,
            'amount' => null,
            'payment_status' => null,
            'created_date' => $createdDate,
            'execution_date' => $executionDate,
            'send_date' => null,
            'payment_due_date' => null,
            'completed_at' => $completedAt,
            'paid_at' => null,
            'updated_at' => $completedAt ?? $createdDate,
        ];
    }
    
    /**
     * Generate attributes for step events with project context
     */
    private function stepEventAttributesForProject($faker, Project $project, \DateTime $createdDate): array
    {
        $category = $faker->randomElement(EventCategory::forEventType(EventType::Step));
        $status = $faker->randomElement([EventStatus::Todo, EventStatus::Done, EventStatus::Cancelled]);
        
        // Date d'exécution : entre la création et la fin du projet (ou maintenant)
        $projectEnd = $project->end_date ?? now();
        $maxExecutionDate = min($projectEnd->getTimestamp(), strtotime('today'));
        
        $executionDate = $faker->dateTimeBetween(
            $createdDate,
            date('Y-m-d H:i:s', $maxExecutionDate)
        );
        
        $completedAt = null;
        
        if ($status === EventStatus::Done) {
            $minCompleted = $executionDate->getTimestamp();
            $maxCompleted = min($minCompleted + (7 * 24 * 60 * 60), time());
            if ($minCompleted < $maxCompleted) {
                $completedAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minCompleted), date('Y-m-d H:i:s', $maxCompleted));
            } else {
                $completedAt = new \DateTime(date('Y-m-d H:i:s', $minCompleted));
            }
        } elseif ($status === EventStatus::Cancelled) {
            $minCancelled = $createdDate->getTimestamp();
            $maxCancelled = time();
            if ($minCancelled < $maxCancelled) {
                $completedAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minCancelled), 'now');
            } else {
                $completedAt = new \DateTime();
            }
        }
        
        return [
            'name' => $this->generateStepName($faker, $category),
            'description' => $this->generateStepDescription($faker, $category),
            'type' => $category->value,
            'status' => $status->value,
            'amount' => null,
            'payment_status' => null,
            'execution_date' => $executionDate,
            'send_date' => null,
            'payment_due_date' => null,
            'completed_at' => $completedAt,
            'paid_at' => null,
            'updated_at' => $completedAt ?? $createdDate,
        ];
    }

    /**
     * Generate attributes for billing events
     */
    private function billingEventAttributes($faker): array
    {
        $category = $faker->randomElement(EventCategory::forEventType(EventType::Billing));
        $status = $faker->randomElement([EventStatus::ToSend, EventStatus::Sent, EventStatus::Cancelled]);
        
        // Generate dates
        $createdDate = $faker->dateTimeBetween('-6 months', 'now');
        
        // Ensure send date is after created date BUT NEVER IN THE FUTURE
        $minSendDate = $createdDate->getTimestamp();
        $maxSendDate = strtotime('today'); // Jamais dans le futur
        $sendDate = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minSendDate), date('Y-m-d H:i:s', $maxSendDate));
        
        $paymentDueDate = null;
        $paidAt = null;
        $paymentStatus = null;
        $completedAt = null;
        
        // Generate amount based on category
        $amount = $this->generateAmount($faker, $category);
        
        if ($status === EventStatus::Sent) {
            // If sent, it MUST have payment information
            $minDueDate = $sendDate->getTimestamp();
            $maxDueDate = $minDueDate + (60 * 24 * 60 * 60); // 60 days
            $paymentDueDate = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minDueDate), date('Y-m-d H:i:s', $maxDueDate));
            
            $paymentStatus = $faker->randomElement([PaymentStatus::Pending, PaymentStatus::Paid]);
            
            if ($paymentStatus === PaymentStatus::Paid) {
                // If paid, generate payment date between send date and now
                $minPaidDate = $sendDate->getTimestamp();
                $maxPaidDate = min($paymentDueDate->getTimestamp() + (30 * 24 * 60 * 60), time());
                if ($minPaidDate < $maxPaidDate) {
                    $paidAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minPaidDate), date('Y-m-d H:i:s', $maxPaidDate));
                } else {
                    $paidAt = new \DateTime(date('Y-m-d H:i:s', $minPaidDate));
                }
            }
            
            $completedAt = $sendDate;
        } elseif ($status === EventStatus::Cancelled) {
            $minCancelled = $createdDate->getTimestamp();
            $maxCancelled = time();
            if ($minCancelled < $maxCancelled) {
                $completedAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minCancelled), 'now');
            } else {
                $completedAt = new \DateTime();
            }
        }
        
        return [
            'name' => $this->generateBillingName($faker, $category),
            'description' => $this->generateBillingDescription($faker, $category, $amount),
            'type' => $category->value,
            'status' => $status->value,
            'amount' => $amount,
            'payment_status' => $paymentStatus?->value,
            'created_date' => $createdDate,
            'execution_date' => null,
            'send_date' => $sendDate,
            'payment_due_date' => $paymentDueDate,
            'completed_at' => $completedAt,
            'paid_at' => $paidAt,
            'updated_at' => $paidAt ?? $completedAt ?? $createdDate,
        ];
    }
    
    /**
     * Generate attributes for billing events with project context
     */
    private function billingEventAttributesForProject($faker, Project $project, \DateTime $createdDate): array
    {
        $category = $faker->randomElement(EventCategory::forEventType(EventType::Billing));
        $status = $faker->randomElement([EventStatus::ToSend, EventStatus::Sent, EventStatus::Cancelled]);
        
        // Date d'envoi : entre la création et la fin du projet (ou maintenant)
        $projectEnd = $project->end_date ?? now();
        $maxSendDate = min($projectEnd->getTimestamp(), strtotime('today'));
        
        $sendDate = $faker->dateTimeBetween(
            $createdDate,
            date('Y-m-d H:i:s', $maxSendDate)
        );
        
        $paymentDueDate = null;
        $paidAt = null;
        $paymentStatus = null;
        $completedAt = null;
        
        // Generate amount based on category
        $amount = $this->generateAmount($faker, $category);
        
        if ($status === EventStatus::Sent) {
            // If sent, it MUST have payment information
            $minDueDate = $sendDate->getTimestamp();
            $maxDueDate = $minDueDate + (60 * 24 * 60 * 60);
            $paymentDueDate = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minDueDate), date('Y-m-d H:i:s', $maxDueDate));
            
            $paymentStatus = $faker->randomElement([PaymentStatus::Pending, PaymentStatus::Paid]);
            
            if ($paymentStatus === PaymentStatus::Paid) {
                $minPaidDate = $sendDate->getTimestamp();
                $maxPaidDate = min($paymentDueDate->getTimestamp() + (30 * 24 * 60 * 60), time());
                if ($minPaidDate < $maxPaidDate) {
                    $paidAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minPaidDate), date('Y-m-d H:i:s', $maxPaidDate));
                } else {
                    $paidAt = new \DateTime(date('Y-m-d H:i:s', $minPaidDate));
                }
            }
            
            $completedAt = $sendDate;
        } elseif ($status === EventStatus::Cancelled) {
            $minCancelled = $createdDate->getTimestamp();
            $maxCancelled = time();
            if ($minCancelled < $maxCancelled) {
                $completedAt = $faker->dateTimeBetween(date('Y-m-d H:i:s', $minCancelled), 'now');
            } else {
                $completedAt = new \DateTime();
            }
        }
        
        return [
            'name' => $this->generateBillingName($faker, $category),
            'description' => $this->generateBillingDescription($faker, $category, $amount),
            'type' => $category->value,
            'status' => $status->value,
            'amount' => $amount,
            'payment_status' => $paymentStatus?->value,
            'execution_date' => null,
            'send_date' => $sendDate,
            'payment_due_date' => $paymentDueDate,
            'completed_at' => $completedAt,
            'paid_at' => $paidAt,
            'updated_at' => $paidAt ?? $completedAt ?? $createdDate,
        ];
    }

    /**
     * Generate step event name
     */
    private function generateStepName($faker, EventCategory $category): string
    {
        $categoryKey = $category->value;
        
        if (isset($this->stepTemplates[$categoryKey])) {
            $template = $faker->randomElement($this->stepTemplates[$categoryKey]);
        } else {
            $template = ucfirst($category->value) . ' - ' . $faker->word();
        }
        
        $replacements = [
            '{module}' => $faker->randomElement($this->modules),
            '{feature}' => $faker->randomElement($this->features),
            '{api}' => $faker->randomElement($this->apis),
            '{number}' => $faker->numberBetween(1, 10),
            '{component}' => $faker->randomElement($this->components),
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Generate billing event name
     */
    private function generateBillingName($faker, EventCategory $category): string
    {
        $categoryKey = $category->value;
        
        if (isset($this->billingTemplates[$categoryKey])) {
            $template = $faker->randomElement($this->billingTemplates[$categoryKey]);
        } else {
            $template = ucfirst($category->label()) . ' #{number}';
        }
        
        $months = [
            'Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
            'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'
        ];
        
        $replacements = [
            '{number}' => $faker->numberBetween(1000, 9999),
            '{month}' => $faker->randomElement($months),
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Generate step description
     */
    private function generateStepDescription($faker, EventCategory $category): ?string
    {
        if ($faker->boolean(70)) { // 70% have descriptions
            $descriptions = [
                'meeting' => "Discussion des points suivants :\n- État d'avancement\n- Prochaines étapes\n- Points de blocage",
                'development' => "Implémentation complète avec tests unitaires et documentation technique.",
                'design' => "Création et validation des éléments visuels selon la charte graphique.",
                'testing' => "Validation complète incluant les cas nominaux et les cas d'erreur.",
                'default' => $faker->text(150),
            ];
            
            return $descriptions[$category->value] ?? $descriptions['default'];
        }
        
        return null;
    }

    /**
     * Generate billing description
     */
    private function generateBillingDescription($faker, EventCategory $category, float $amount): ?string
    {
        if ($faker->boolean(60)) { // 60% have descriptions
            $formattedAmount = number_format($amount, 2, ',', ' ') . ' €';
            
            $descriptions = [
                'invoice' => "Facturation des prestations réalisées.\nMontant HT : {$formattedAmount}",
                'quote' => "Proposition commerciale détaillée.\nMontant estimé : {$formattedAmount}",
                'deposit' => "Acompte sur le montant total du projet.\nMontant : {$formattedAmount}",
                'default' => "Document de facturation.\nMontant : {$formattedAmount}",
            ];
            
            $base = $descriptions[$category->value] ?? $descriptions['default'];
            
            if ($faker->boolean(40)) {
                $base .= "\n\n" . $faker->text(100);
            }
            
            return $base;
        }
        
        return null;
    }

    /**
     * Generate realistic amounts based on category
     */
    private function generateAmount($faker, EventCategory $category): float
    {
        // Generate amounts based on category type, with realistic ranges
        $ranges = match($category) {
            EventCategory::Delivery => [2000, 25000], // Major deliverables
            EventCategory::Execution => [1000, 15000], // Development work
            EventCategory::Consultation => [500, 5000], // Consultation sessions
            EventCategory::Planning => [500, 3000], // Planning phases
            EventCategory::Training => [800, 5000], // Training sessions
            EventCategory::Maintenance => [300, 2000], // Maintenance work
            EventCategory::Review => [300, 1500], // Review sessions
            EventCategory::Research => [500, 3000], // Research work
            default => [500, 10000], // Default range
        };
        
        $amount = $faker->randomFloat(2, $ranges[0], $ranges[1]);
        
        // Round to nice numbers
        if ($amount > 1000) {
            $amount = round($amount / 100) * 100;
        } elseif ($amount > 100) {
            $amount = round($amount / 10) * 10;
        }
        
        return $amount;
    }

    /**
     * Create a step event
     */
    public function step(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            return array_merge(
                ['event_type' => EventType::Step->value],
                $this->stepEventAttributes($faker)
            );
        });
    }

    /**
     * Create a billing event
     */
    public function billing(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            return array_merge(
                ['event_type' => EventType::Billing->value],
                $this->billingEventAttributes($faker)
            );
        });
    }

    /**
     * Create a todo/to_send event
     */
    public function pending(): static
    {
        return $this->state(function (array $attributes) {
            $eventType = EventType::from($attributes['event_type'] ?? EventType::Step->value);
            return [
                'status' => $eventType === EventType::Step ? EventStatus::Todo->value : EventStatus::ToSend->value,
                'completed_at' => null,
            ];
        });
    }

    /**
     * Create a completed event
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            $eventType = EventType::from($attributes['event_type'] ?? EventType::Step->value);
            
            $baseAttributes = [
                'status' => $eventType === EventType::Step ? EventStatus::Done->value : EventStatus::Sent->value,
                'completed_at' => $faker->dateTimeBetween('-1 month', 'now'),
            ];
            
            // For billing events, ensure we have payment information
            if ($eventType === EventType::Billing) {
                $completedAt = $baseAttributes['completed_at'];
                $sendDate = $faker->dateTimeBetween('-2 months', $completedAt);
                
                $minDueDate = $sendDate->getTimestamp();
                $maxDueDate = $minDueDate + (60 * 24 * 60 * 60); // 60 days
                
                // Ensure payment_due_date is after send_date
                $paymentDueDate = $faker->dateTimeBetween($sendDate, date('Y-m-d H:i:s', $maxDueDate));
                
                $baseAttributes = array_merge($baseAttributes, [
                    'send_date' => $sendDate,
                    'payment_due_date' => $paymentDueDate,
                    'payment_status' => $faker->randomElement([PaymentStatus::Pending, PaymentStatus::Paid]),
                ]);
            }
            
            return $baseAttributes;
        });
    }

    /**
     * Create a paid billing event
     */
    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            
            $paidAt = $faker->dateTimeBetween('-1 month', 'now');
            $sendDate = $faker->dateTimeBetween('-2 months', $paidAt);
            
            $minDueDate = $sendDate->getTimestamp();
            $maxDueDate = $minDueDate + (60 * 24 * 60 * 60); // 60 days
            
            // Ensure payment_due_date is after send_date
            $paymentDueDate = $faker->dateTimeBetween($sendDate, date('Y-m-d H:i:s', $maxDueDate));
            
            return [
                'event_type' => EventType::Billing->value,
                'status' => EventStatus::Sent->value,
                'payment_status' => PaymentStatus::Paid->value,
                'send_date' => $sendDate,
                'payment_due_date' => $paymentDueDate,
                'paid_at' => $paidAt,
                'completed_at' => $sendDate,
            ];
        });
    }

    /**
     * Create an overdue event
     */
    public function overdue(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            $eventType = EventType::from($attributes['event_type'] ?? EventType::Step->value);
            
            if ($eventType === EventType::Step) {
                return [
                    'status' => EventStatus::Todo->value,
                    'execution_date' => $faker->dateTimeBetween('-2 months', '-1 day'),
                ];
            } else {
                return [
                    'status' => EventStatus::ToSend->value,
                    'send_date' => $faker->dateTimeBetween('-2 months', '-1 day'),
                ];
            }
        });
    }
}