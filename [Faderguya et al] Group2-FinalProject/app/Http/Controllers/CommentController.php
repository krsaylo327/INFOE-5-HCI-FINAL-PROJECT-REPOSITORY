<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * STORE: Store a new comment associated with a specific ticket.
     */
    public function store(Request $request, Ticket $ticket)
    {
        // IMPORTANT: User must be able to view the ticket to comment on it.
        // Can reuse the view policy here for strong security.
        $this->authorize('view', $ticket);

        $validated = $request->validate([
            'content' => 'required|string|min:5',
        ]);

        $comment = $ticket->comments()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        // Touch the ticket to update its timestamp, bringing it to the top of the admin's list.
        $ticket->touch();

        // Return the comment, eager-loading the user details for the frontend display.
        return response()->json($comment->load('user:id,name,role'), 201);
    }
}
