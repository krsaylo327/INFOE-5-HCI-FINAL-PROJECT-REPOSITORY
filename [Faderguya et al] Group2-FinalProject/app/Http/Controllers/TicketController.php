<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    /**
     * READ: Display a listing of tickets based on user role.
     */
    public function index()
    {
        $user = auth()->user();
        $query = Ticket::with('creator:id,name,role', 'assignedTo:id,name,role', 'comments');

        if ($user->role === 'student') {
            // Student: Only see tickets they created
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'faculty') {
            // Faculty: See tickets they created OR tickets assigned to them
            $query->where('user_id', $user->id)
                ->orWhere('assigned_to', $user->id);
        }
        // Admin: Sees all (no where clause needed)

        return response()->json($query->latest()->get());
    }

    /**
     * CREATE: Store a newly created ticket.
     */
    public function store(Request $request)
    {
        // Policy check: All authenticated users (student, faculty, admin) can create.
        $this->authorize('create', Ticket::class);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => ['required', 'string', Rule::in(['Low', 'Medium', 'High'])],
            'attachment' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('tickets', 'public');
        }

        $ticket = Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $validated['subject'],
            'description' => $validated['description'],
            'priority' => $validated['priority'],
            'attachment' => $attachmentPath,
            'status' => 'Open', // Defaults to 'Open' (Awaiting Admin Acceptance)
        ]);

        return response()->json($ticket->load('creator'), 201);
    }

    /**
     * READ: Display the specified ticket.
     */
    public function show(Ticket $ticket)
    {
        // Policy check: Ensures user is the creator, assignedTo, or admin.
        $this->authorize('view', $ticket);

        // Load comments and user details for the response
        $ticket->load('comments.user');

        return response()->json($ticket);
    }

    /**
     * UPDATE: Update the specified ticket.
     */
    public function update(Request $request, Ticket $ticket)
    {
        // Policy check: Crucial step enforcing status and role rules.
        $this->authorize('update', $ticket);

        $user = auth()->user();

        // Define validation rules based on role
        if ($user->role === 'admin' || $user->role === 'faculty') {
            // Admin/Faculty (when assigned) can update ALL fields.
            $validated = $request->validate([
                'subject' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'priority' => ['sometimes', 'string', Rule::in(['Low', 'Medium', 'High'])],
                'status' => ['sometimes', 'string', Rule::in(['Open', 'In Progress', 'Resolved', 'Closed'])],
                'assigned_to' => 'sometimes|nullable|exists:users,id', // Only Admins can reassign, but the policy handles overall update permission.
            ]);
        } else {
            // Student (only if status is 'Open') can only update subject/description.
            $validated = $request->validate([
                'subject' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
            ]);
        }

        $ticket->update($validated);

        return response()->json($ticket->load('creator'), 200);
    }

    /**
     * DELETE: Remove the specified ticket from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Policy check: Only Admins can delete.
        $this->authorize('delete', $ticket);

        $ticket->delete();

        return response()->json(['message' => 'Ticket successfully deleted.'], 204); // 204 No Content
    }
}
