<?php

namespace App\Http\Controllers\Events;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Services\Events\EventDetailService;
use App\Services\Event\EventService;
use App\DTOs\EventDTO;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class EventDetailController extends Controller
{
    public function __construct(
        private readonly EventDetailService $eventDetailService,
    ) {}

    /**
     * Display event detail page with skeleton pattern
     */
    public function show(int $eventId): Response
    {
        // Pattern skeleton : page avec données skeleton + ID pour fetch AJAX
        return Inertia::render('Events/Detail/Index', [
            'eventId' => $eventId,
            'skeletonData' => $this->eventDetailService->getSkeletonData(),
            'data' => Inertia::optional(fn () =>
                $this->eventDetailService->getCompleteData($eventId)
            ),
        ]);
    }

}
