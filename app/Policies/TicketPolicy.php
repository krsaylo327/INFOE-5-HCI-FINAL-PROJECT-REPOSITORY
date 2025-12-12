<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Auth\Access\Response;

class TicketPolicy
{
    /**
     * The Super Admin Check: Grant all permissions if the user is an 'admin'.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Admins bypass all checks
        if ($user->role === 'admin') {
            return true;
        }
        return null;
    }

    /**
     * Determine if the user can view the model.
     * Allowed if:
     * 1. Creator (Student/Faculty)
     * 2. Assigned Resolver (Faculty)
     */
    public function view(User $user, Ticket $ticket): Response
    {
        // Check 1: User is the creator
        if ($ticket->user_id === $user->id) {
            return Response::allow();
        }

        // Check 2: User is the assigned resolver AND is Faculty
        if ($user->role === 'faculty' && $ticket->assigned_to === $user->id) {
            return Response::allow();
        }

        return Response::deny('You do not have permission to view this ticket.');
    }

    /**
     * Determine if the user can create models.
     * All roles (student, faculty, admin) can create tickets.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine if the user can update the model.
     * Allowed if:
     * 1. Creator AND status is 'Open' (Student/Faculty)
     * 2. Assigned Resolver (Faculty)
     */
    public function update(User $user, Ticket $ticket): Response
    {
        // Check 1: Is the user the creator AND is the status 'Open'?
        if ($ticket->user_id === $user->id && $ticket->status === 'Open') {
            return Response::allow();
        }

        // Check 2: Is the user Faculty AND assigned to the ticket?
        if ($user->role === 'faculty' && $ticket->assigned_to === $user->id) {
            return Response::allow();
        }

        return Response::deny('You are not authorized to edit this ticket at this time.');
    }

    /**
     * Determine if the user can delete the model.
     * Only Admins are allowed (handled by before()).
     */
    public function delete(User $user, Ticket $ticket): Response
    {
        return Response::deny('Only administrators can delete tickets.');
    }
}
