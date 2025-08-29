<?php

namespace App\Http\Controllers\Clients;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Services\Clients\ClientDetailService;
use App\DTOs\ClientDTO;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class ClientDetailController extends Controller
{
    public function __construct(
        private readonly ClientDetailService $clientDetailService
    ) {}

    /**
     * Display client details with skeleton data for immediate rendering
     */
    public function show(int $clientId, Request $request): Response
    {
        $skeletonData = $this->clientDetailService->getSkeletonData($clientId);

        return Inertia::render('Clients/Detail/Index', [
            'skeletonData' => array_merge($skeletonData, [
                'skeleton_mode' => true
            ]),
            'data' => Inertia::optional(fn () =>
                $this->clientDetailService->getCompleteData($clientId)
            ),
        ]);
    }

}
