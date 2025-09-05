<?php

namespace App\Services;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;

class JsonLdService
{
    private array $baseSchema = [];
    private array $pageSchema = [];

    public function __construct()
    {
        $this->baseSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => config('app.name', 'Stone'),
            'description' => 'Solution de gestion tout-en-un gratuite et open source pour freelances français. Clients, projets, facturation en toute simplicité.',
            'url' => config('app.url'),
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'EUR'
            ],
            'author' => [
                '@type' => 'Person',
                'name' => 'Micha Megret',
                'jobTitle' => 'Développeur Web Freelance',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Évian-les-Bains',
                    'addressCountry' => 'FR'
                ]
            ],
            'inLanguage' => 'fr-FR',
            'softwareVersion' => '1.0',
            'dateCreated' => '2025',
            'license' => 'Open Source'
        ];
    }

    public function setPageSchema(array $schema): self
    {
        $this->pageSchema = $schema;
        return $this;
    }

    public function addPageSchema(array $schema): self
    {
        $this->pageSchema = array_merge($this->pageSchema, $schema);
        return $this;
    }

    public function generate(): string
    {
        $merged = $this->mergeSchemas();
        return json_encode($merged, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    private function mergeSchemas(): array
    {
        if (empty($this->pageSchema)) {
            return $this->baseSchema;
        }

        // Si la page a son propre schéma complet, on le fusionne intelligemment
        $merged = $this->pageSchema;

        // Assurer que @context est présent
        if (!isset($merged['@context'])) {
            $merged['@context'] = $this->baseSchema['@context'];
        }

        // Si c'est une WebPage avec mainEntity, on préserve l'organisation de base
        if (isset($merged['@type']) && $merged['@type'] === 'WebPage') {
            // Ajouter l'organisation comme publisher si pas déjà défini
            if (!isset($merged['publisher'])) {
                $merged['publisher'] = [
                    '@type' => $this->baseSchema['@type'],
                    'name' => $this->baseSchema['name'],
                    'url' => $this->baseSchema['url'],
                    'author' => $this->baseSchema['author']
                ];
            }

            // Si mainEntity existe et est une SoftwareApplication, on la complète
            if (isset($merged['mainEntity']) && 
                is_array($merged['mainEntity']) && 
                isset($merged['mainEntity']['@type']) && 
                $merged['mainEntity']['@type'] === 'SoftwareApplication') {
                
                $merged['mainEntity'] = array_merge(
                    $this->baseSchema,
                    $merged['mainEntity']
                );
            }
        } else {
            // Pour d'autres types de pages, on fusionne directement
            $merged = array_merge($this->baseSchema, $merged);
        }

        return $merged;
    }

    /**
     * Créer un schéma WebPage standard
     */
    public static function webPage(string $name, string $description, string $url = null): array
    {
        return [
            '@type' => 'WebPage',
            'name' => $name,
            'description' => $description,
            'url' => $url ?? request()->url(),
        ];
    }

    /**
     * Créer un schéma avec mainEntity pour SoftwareApplication
     */
    public static function softwareApplicationPage(string $name, string $description, array $additionalData = []): array
    {
        return [
            '@type' => 'WebPage',
            'name' => $name,
            'description' => $description,
            'url' => request()->url(),
            'mainEntity' => array_merge([
                '@type' => 'SoftwareApplication',
                'name' => $name,
                'description' => $description,
            ], $additionalData)
        ];
    }

    /**
     * Méthode helper pour les vues Blade
     */
    public static function render(array $pageSchema = []): string
    {
        $service = new self();
        if (!empty($pageSchema)) {
            $service->setPageSchema($pageSchema);
        }
        return $service->generate();
    }
}