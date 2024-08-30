<?php

namespace App\Http\Controllers;


use Inertia\Inertia;
use App\Models\Event;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class CandidateController extends Controller
{
    public function __construct()
    {
        // Middleware untuk izin
        $this->middleware('permission:create candidate', ['only' => ['create', 'store']]);
        $this->middleware('permission:read candidate', ['only' => ['index', 'show']]);
        $this->middleware('permission:update candidate', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete candidate', ['only' => ['destroy']]);
    }


    /**
     * Display a listing of the candidates.
     */
    public function index(Request $request)
    {
        $candidates = Candidate::query()
            ->with('event')
            ->when($request->input('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('region', 'like', "%{$search}%");
            })
            ->orderBy($request->input('field', 'created_at'), $request->input('order', 'desc'))
            ->paginate($request->input('perPage', 10));

        return Inertia::render('Candidate/Index', [
            'title' => 'Candidate List',
            'filters' => $request->only('search', 'field', 'order'),
            'candidates' => $candidates,
        ]);
    }

    /**
     * Show the form for creating a new candidate.
     */
    public function create()
    {
        $events = Event::all(['id', 'name']);
        return response()->json($events); // Return as JSON
    }

    /**
     * Store a newly created candidate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:7048',
            'description' => 'nullable|string',
            'event_id' => 'required|exists:events,id',
        ]);

        $event = Event::findOrFail($request->event_id);

        // Handle photo upload
        $path = $request->file('photo')->store('photos', 'public');

        Candidate::create([
            'name' => $request->name,
            'photo' => $path, // Store the path to the uploaded photo
            'description' => $request->description,
            'event_id' => $request->event_id,
            'regional_id' => $event->regional_id,
            'regency_id' => $event->regency_id,
        ]);

        return Redirect::route('candidates.index')->with('success', 'Candidate created successfully.');
    }


    /**
     * Show the form for editing the specified candidate.
     */

    public function edit(Candidate $candidate)
    {
        // Mengambil hanya event yang berstatus 'Ready'
        $events = Event::where('status', 'Ready')->get(['id', 'name', 'status']);

        return Inertia::render('Candidate/Edit', [
            'candidate' => $candidate,
            'events' => $events,
        ]);
    }
    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'regional_id' => 'nullable|exists:regionals,id',
            'regency_id' => 'nullable|exists:regencies,id',
            'photo' => 'nullable|image|max:7048', // Validasi file foto
        ]);

        if ($request->hasFile('photo')) {
            // Proses upload foto
            $path = $request->file('photo')->store('public/photos');
            $data['photo'] = Storage::url($path);
        }
        $candidate->update($data);

        return redirect()->route('candidates.index')->with('success', 'Candidate updated successfully.');
    }


    /**
     * Remove the specified candidate from storage.
     */
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();

        return Redirect::route('candidates.index')->with('success', 'Candidate deleted successfully.');
    }
}
