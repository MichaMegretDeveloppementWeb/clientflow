<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\GitHubService;

class GitHubController extends Controller
{
    public function __construct(
        private readonly GitHubService $githubService
    ) {}
    
    /**
     * Récupère les discussions récentes via AJAX
     */
    public function discussions()
    {
        $discussions = $this->githubService->getRecentDiscussions(10);
        
        return response()->json([
            'discussions' => $discussions,
            'count' => count($discussions)
        ]);
    }
}