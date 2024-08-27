<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create event', ['only' => ['create', 'store']]);
        $this->middleware('permission:read event', ['only' => ['index', 'show']]);
        $this->middleware('permission:update event', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete event', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {
        $events = Event::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($request->input('field', 'created_at'), $request->input('order', 'desc'))
            ->paginate($request->input('perPage', 10));

        $events->transform(function ($event) {
            $event->regional_name = $this->fetchProvinceName($event->regional_id);
            $event->regency_name = $this->fetchRegencyName($event->regional_id, $event->regency_id);
            return $event;
        });

        return Inertia::render('Event/Index', [
            'title' => 'Event List',
            'filters' => $request->only('search', 'field', 'order'),
            'events' => $events,
        ]);
    }

    public function create()
    {
        $provinces = $this->getProvinces();

        return Inertia::render('Event/Create', [
            'title' => 'Create Event',
            'provinces' => $provinces,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'voting_type' => 'required|string',
            'description' => 'nullable|string',
            'regional_id' => 'required|integer',
            'regency_id' => 'required|integer',
        ]);

        Event::create($validated);

        return redirect()->route('event.index')->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        // Fetch the names of the province and regency
        $event->regional_name = $this->fetchProvinceName($event->regional_id);
        $event->regency_name = $this->fetchRegencyName($event->regional_id, $event->regency_id);

        return Inertia::render('Event/Edit', [
            'title' => 'Edit Event',
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'voting_type' => 'required|string',
            'description' => 'nullable|string',
            'regional_id' => 'required|integer',
            'regency_id' => 'required|integer',
        ]);

        $event->update($validated);

        return Redirect::route('event.index')->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return Redirect::route('event.index')->with('success', 'Event deleted successfully.');
    }

    private function getProvinces()
    {
        $response = Http::get("https://faisal-ltf.github.io/api-wilayah-indonesia/api/provinces.json");
        return $response->successful() ? $response->json() : [];
    }

    private function fetchProvinceName($provinceId)
    {
        $provinces = $this->getProvinces();
        $province = collect($provinces)->firstWhere('id', $provinceId);
        return $province['name'] ?? 'N/A';
    }

    private function fetchRegencyName($provinceId, $regencyId)
    {
        $response = Http::get("https://faisal-ltf.github.io/api-wilayah-indonesia/api/regencies/{$provinceId}.json");
        if ($response->successful()) {
            $regencies = $response->json();
            $regency = collect($regencies)->firstWhere('id', $regencyId);
            return $regency['name'] ?? 'N/A';
        }
        return 'N/A';
    }
}
