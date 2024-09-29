<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Event;
use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;

class CandidateController extends Controller
{
    // Middleware for permissions
    public function __construct()
    {
        $this->middleware('permission:create candidate', ['only' => ['create', 'store']]);
        $this->middleware('permission:read candidate', ['only' => ['index', 'show']]);
        $this->middleware('permission:update candidate', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete candidate', ['only' => ['destroy']]);
    }

    // Display listing of candidates
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

    // Show form for creating new candidate
    public function create()
    {
        // Mendapatkan event yang berstatus 'ready' atau 'progress'
        $events = Event::whereIn('status', ['ready', 'progress'])->get(['id', 'name']);
        return response()->json($events); // Return as JSON for frontend
    }

    // Store new candidate
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
        Log::info('Photo uploaded to: ' . $path); // Logging for debugging

        Candidate::create([
            'name' => $request->name,
            'photo' => $path, // Store photo path
            'description' => $request->description,
            'event_id' => $request->event_id,
            'regional_id' => $event->regional_id,
            'regency_id' => $event->regency_id,
        ]);

        return Redirect::route('candidates.index')->with('success', 'Candidate created successfully.');
    }

    // Update candidate data
    public function update(Request $request, Candidate $candidate)
    {
        $data = $request->validate([
            'name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'photo' => 'nullable|image|max:7048',
        ]);

        // Update non-photo fields
        $candidate->fill([
            'name' => $data['name'],
            'description' => $data['description'],
            'event_id' => $data['event_id'],
        ]);

        // Handle photo update
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($candidate->photo) {
                Storage::disk('public')->delete($candidate->photo);
            }

            // Store new photo
            $path = $request->file('photo')->store('photos', 'public');
            $candidate->photo = $path;
            Log::info('New photo uploaded to: ' . $path);
        }

        $candidate->save();

        return redirect()->route('candidates.index')->with('success', 'Candidate updated successfully.');
    }

    // Update candidate photo
    public function editFileFoto(Request $request, Candidate $candidate)
    {
        $request->validate([
            'photo' => 'nullable|image|max:7048',
        ]);

        if ($request->hasFile('photo')) {
            if ($candidate->photo) {
                Storage::delete('public/' . $candidate->photo); // Delete old photo
            }

            $path = $request->file('photo')->store('photos', 'public');
            Log::info('New photo uploaded to: ' . $path); // Log new photo path

            $candidate->update(['photo' => $path]);

            return true;
        }

        return false;
    }

    // Remove candidate from storage
    public function destroy(Candidate $candidate)
    {
        if ($candidate->photo) {
            Storage::delete('public/' . $candidate->photo); // Delete photo from storage
        }

        $candidate->delete();

        return Redirect::route('candidates.index')->with('success', 'Candidate deleted successfully.');
    }
}
