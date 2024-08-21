<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\Event;
use Illuminate\Http\Request;

class CandidateController extends Controller
{
    // Fetch all candidates for a specific event
    public function index($eventId)
    {
        $candidates = Candidate::where('event_id', $eventId)->get();
        return response()->json($candidates);
    }

    // Store a new candidate
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'required|string',
            'description' => 'nullable|string',
            'region' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
        ]);

        $candidate = Candidate::create($validated);

        return response()->json($candidate, 201);
    }

    // Update a candidate
    public function update(Request $request, Candidate $candidate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'photo' => 'required|string',
            'description' => 'nullable|string',
            'region' => 'required|string|max:255',
            'event_id' => 'required|exists:events,id',
        ]);

        $candidate->update($validated);

        return response()->json($candidate);
    }

    // Delete a candidate
    public function destroy(Candidate $candidate)
    {
        $candidate->delete();
        return response()->json(['message' => 'Candidate deleted successfully']);
    }
}
