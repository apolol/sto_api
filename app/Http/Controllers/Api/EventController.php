<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CRM\Brand;
use App\Models\EventCalendar;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EventController extends Controller
{
    public function index(): JsonResponse
    {
        $events = EventCalendar::orderByDate('DESK')->paginate(20);
        $eventsToday = EventCalendar::where('date', date('Y-m-d'))->count();
        return response()->json([
            'events' => $events,
            'count_today' => $eventsToday
        ]);
    }

    public function store(Request $request):JsonResponse
    {
        $event = new EventCalendar();
        $event->title = $request?->title ?? '';
        $event->description = $request?->description ?? '';
        $event->date = $request?->date ?? '';
        $event->car_id = $request?->car_id ?? '';
        $event->save();

        return response()->json([],300);
    }

    public function update(Request $request, EventCalendar $eventCalendar):JsonResponse
    {
        $eventCalendar->status = ($eventCalendar == 0 ? 1 : 0);
        $eventCalendar->update();

        return response()->json([],300);
    }

    public function destroy(EventCalendar $eventCalendar):JsonResponse
    {
        $eventCalendar->delete();

        return response()->json([],300);
    }
}
