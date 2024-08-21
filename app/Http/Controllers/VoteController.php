<?php

namespace App\Http\Controllers;

use App\Models\Vote;
use App\Models\Candidate;
use Illuminate\Http\Request;

class VoteController extends Controller
{
    // Store a new vote
    public function store(Request $request, $candidateId)
    {
        $candidate = Candidate::findOrFail($candidateId);

        $vote = new Vote();
        $vote->user_id = auth()->id();
        $vote->candidate_id = $candidate->id;
        $vote->event_id = $candidate->event_id;
        $vote->is_paid = $request->input('is_paid', false); // Adjust based on payment logic
        $vote->save();

        return response()->json(['message' => 'Vote recorded successfully']);
    }

    // Get vote count for a candidate
    public function count($candidateId)
    {
        $count = Vote::where('candidate_id', $candidateId)->count();
        return response()->json(['vote_count' => $count]);
    }
}
