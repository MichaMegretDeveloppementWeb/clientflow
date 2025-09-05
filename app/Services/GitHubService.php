<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GitHubService
{
    private string $baseUrl = 'https://api.github.com';
    private string $repository;
    
    public function __construct()
    {
        // Extraire owner/repo depuis l'URL GitHub (ex: https://github.com/stone-app/stone)
        $githubUrl = config('social.github', '');
        if (preg_match('#github\.com/([^/]+/[^/]+)#', $githubUrl, $matches)) {
            $this->repository = $matches[1];
        }
    }
    
    /**
     * Récupère les discussions récentes du repository
     */
    public function getRecentDiscussions(int $limit = 10): array
    {
        if (empty($this->repository)) {
            return [];
        }
        
        $cacheKey = "github_discussions_{$this->repository}_{$limit}";
        
        return Cache::remember($cacheKey, now()->addMinutes(30), function () use ($limit) {
            try {
                $response = Http::timeout(10)
                    ->get("{$this->baseUrl}/repos/{$this->repository}/discussions", [
                        'per_page' => $limit,
                        'sort' => 'updated',
                        'direction' => 'desc'
                    ]);
                
                if (!$response->successful()) {
                    Log::warning('GitHub API request failed', [
                        'status' => $response->status(),
                        'repository' => $this->repository
                    ]);
                    return [];
                }
                
                $discussions = $response->json();
                
                return array_map(function ($discussion) {
                    return [
                        'id' => $discussion['number'],
                        'title' => $discussion['title'],
                        'author' => $discussion['user']['login'],
                        'avatar_url' => $discussion['user']['avatar_url'],
                        'created_at' => $discussion['created_at'],
                        'updated_at' => $discussion['updated_at'],
                        'comments_count' => $discussion['comments'] ?? 0,
                        'category' => $discussion['category']['name'] ?? 'Général',
                        'html_url' => $discussion['html_url'],
                        'body_preview' => $this->truncateText($discussion['body'] ?? '', 150)
                    ];
                }, $discussions);
                
            } catch (\Exception $e) {
                Log::error('Error fetching GitHub discussions', [
                    'error' => $e->getMessage(),
                    'repository' => $this->repository
                ]);
                return [];
            }
        });
    }
    
    /**
     * Tronque le texte à la longueur spécifiée
     */
    private function truncateText(string $text, int $length): string
    {
        $text = strip_tags($text);
        if (strlen($text) <= $length) {
            return $text;
        }
        
        return substr($text, 0, $length) . '...';
    }
    
    /**
     * Formate la date pour l'affichage
     */
    public static function formatDate(string $dateString): string
    {
        $date = new \DateTime($dateString);
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h == 0) {
                return $diff->i . ' min';
            }
            return $diff->h . 'h';
        } elseif ($diff->days < 7) {
            return $diff->days . 'j';
        } else {
            return $date->format('d/m');
        }
    }
}