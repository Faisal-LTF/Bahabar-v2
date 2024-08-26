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
        // Middleware untuk izin
        $this->middleware('permission:create event', ['only' => ['create', 'store']]);
        $this->middleware('permission:read event', ['only' => ['index', 'show']]);
        $this->middleware('permission:update event', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete event', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of the events.
     */
    public function index(Request $request)
    {
        $events = Event::query()
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy($request->input('field', 'created_at'), $request->input('order', 'desc'))
            ->paginate($request->input('perPage', 10));

        // Ambil nama provinsi dan kabupaten/kota dari API
        $events->transform(function ($event) {
            // Ambil data semua provinsi
            $regionalResponse = Http::get("https://faisal-ltf.github.io/api-wilayah-indonesia/api/provinces.json");
            if ($regionalResponse->status() == 200) {
                $allProvinces = $regionalResponse->json();

                // Cari nama provinsi berdasarkan ID
                $selectedProvince = collect($allProvinces)->firstWhere('id', $event->regional_id);
                $event->regional_name = $selectedProvince['name'] ?? 'N/A';
            } else {
                $event->regional_name = 'N/A'; // Jika data tidak ditemukan atau terjadi error
            }

            // Ambil data semua kabupaten/kota berdasarkan ID provinsi
            $regenciesResponse = Http::get("https://faisal-ltf.github.io/api-wilayah-indonesia/api/regencies/{$event->regional_id}.json");
            if ($regenciesResponse->status() == 200) {
                $allRegencies = $regenciesResponse->json();

                // Cari nama kabupaten/kota berdasarkan ID kabupaten/kota
                $selectedRegency = collect($allRegencies)->firstWhere('id', $event->regency_id);
                $event->regency_name = $selectedRegency['name'] ?? 'N/A';
            } else {
                $event->regency_name = 'N/A'; // Jika data tidak ditemukan atau terjadi error
            }
            return $event;
        });

        return Inertia::render('Event/Index', [
            'title' => 'Event List',
            'filters' => $request->only('search', 'field', 'order'),
            'events' => $events,
        ]);
    }


    /**
     * Show the form for creating a new event.
     */
    public function create()
    {
        // Ambil data provinsi dari ApiController
        $provinces = app(ApiController::class)->getProvinces()->getData();

        // Mengembalikan view untuk halaman pembuatan event dengan data provinsi
        return Inertia::render('Event/Create', [
            'title' => 'Create Event',
            'provinces' => $provinces,
        ]);
    }


    /**
     * Store a newly created event in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'voting_type' => 'required|string',
            'description' => 'nullable|string',
            'regional_id' => 'required|integer', // Validasi ID provinsi
            'regency_id' => 'required|integer', // Validasi ID kabupaten/kota
        ]);

        Event::create($validated);

        return redirect()->route('event.index')->with('success', 'Event created successfully.');
    }


    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event)
    {
        return Inertia::render('Event/Edit', [
            'title' => 'Edit Event',
            'event' => $event,
        ]);
    }

    /**
     * Update the specified event in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'voting_type' => 'required|in:gratis,berbayar',
            'description' => 'nullable|string',
        ]);

        $event->update($validated);

        return Redirect::route('event.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified event from storage.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return Redirect::route('event.index')->with('success', 'Event deleted successfully.');
    }
}
