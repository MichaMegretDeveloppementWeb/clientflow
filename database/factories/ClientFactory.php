<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    protected $model = Client::class;

    /**
     * French company names for realistic data
     */
    private array $companyPrefixes = [
        'Société', 'Entreprise', 'Groupe', 'Cabinet', 'Agence',
        'Studio', 'Atelier', 'Compagnie', 'Institut', 'Centre'
    ];

    private array $companySuffixes = [
        'Conseil', 'Services', 'Solutions', 'Digital', 'Tech',
        'Innovation', 'Développement', 'Marketing', 'Communication', 'Design'
    ];

    /**
     * French business sectors
     */
    private array $sectors = [
        'informatique', 'marketing', 'communication', 'finance', 'immobilier',
        'santé', 'éducation', 'transport', 'énergie', 'commerce'
    ];

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('fr_FR');

        // Generate realistic French company name
        $companyName = $this->generateCompanyName($faker);

        // Sometimes the client is an individual (freelance) without company
        $isIndividual = $faker->boolean(20); // 20% are individuals

        return [
            'name' => $faker->name(),
            'email' => $faker->unique()->companyEmail(),
            'phone' => $this->generateFrenchPhoneNumber($faker),
            'company' => $isIndividual ? null : $companyName,
            'address' => $this->generateFrenchAddress($faker),
            'notes' => $faker->optional(0.6)->text(200), // 60% have notes
            'user_id' => User::first()?->id ?? User::factory(),
        ];
    }

    /**
     * Generate a realistic French company name
     */
    private function generateCompanyName($faker): string
    {
        $type = $faker->randomElement([1, 2, 3, 4]);

        return match($type) {
            1 => $faker->randomElement($this->companyPrefixes) . ' ' . $faker->lastName() . ' ' . $faker->randomElement($this->companySuffixes),
            2 => $faker->lastName() . ' & ' . $faker->lastName(),
            3 => strtoupper($faker->lexify('???')) . ' ' . $faker->randomElement($this->companySuffixes),
            4 => $faker->company(),
            default => $faker->company(),
        };
    }

    /**
     * Generate a French phone number
     */
    private function generateFrenchPhoneNumber($faker): string
    {
        $formats = [
            '01 ## ## ## ##', // Paris
            '02 ## ## ## ##', // Northwest
            '03 ## ## ## ##', // Northeast
            '04 ## ## ## ##', // Southeast
            '05 ## ## ## ##', // Southwest
            '06 ## ## ## ##', // Mobile
            '07 ## ## ## ##', // Mobile
            '09 ## ## ## ##', // VoIP
        ];

        return $faker->numerify($faker->randomElement($formats));
    }

    /**
     * Generate a realistic French address
     */
    private function generateFrenchAddress($faker): string
    {
        $streetNumber = $faker->buildingNumber();
        $streetName = $faker->streetName();
        $postcode = $faker->postcode();
        $city = $faker->city();

        // Sometimes add additional info
        $additional = '';
        if ($faker->boolean(30)) {
            $additional = "\n" . $faker->randomElement(['Bâtiment ', 'Étage ', 'Bureau ']) . $faker->randomElement(['A', 'B', 'C', '1', '2', '3']);
        }

        return "{$streetNumber} {$streetName}{$additional}\n{$postcode} {$city}";
    }

    /**
     * Indicate that the client is a company
     */
    public function company(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            return [
                'company' => $this->generateCompanyName($faker),
            ];
        });
    }

    /**
     * Indicate that the client is an individual
     */
    public function individual(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'company' => null,
            ];
        });
    }

    /**
     * Indicate that the client has detailed notes
     */
    public function withNotes(): static
    {
        return $this->state(function (array $attributes) {
            $faker = \Faker\Factory::create('fr_FR');
            $sector = $faker->randomElement($this->sectors);
            return [
                'notes' => "Client dans le secteur {$sector}. " . $faker->text(150),
            ];
        });
    }
}
