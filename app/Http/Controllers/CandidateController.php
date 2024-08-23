<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;
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
            'photo' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
            'region' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
        ]);

        $path = $request->file('photo')->store('photos', 'public');

        Candidate::create([
            'name' => $request->name,
            'photo' => $path,
            'description' => $request->description,
            'region' => $request->region,
            'event_id' => $request->event_id,
        ]);

        return Redirect::route('candidates.index')->with('success', 'Candidate created successfully.');
    }

    /**
     * Show the form for editing the specified candidate.
     */
    public function edit(Candidate $candidate)
    {
        $events = Event::all();

        return Inertia::render('Candidate/Edit', [
            'title' => 'Edit Candidate',
            'candidate' => $candidate,
            'events' => $events,
        ]);
    }

    /**
     * Update the specified candidate in storage.
     */
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'description' => 'nullable|string',
            'region' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('photos', 'public');
            $data['photo'] = $path;
        }

        $candidate->update($data);

        return Redirect::route('candidates.index')->with('success', 'Candidate updated successfully.');
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
