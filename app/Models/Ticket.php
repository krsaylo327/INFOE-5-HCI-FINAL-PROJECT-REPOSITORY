<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * These fields are allowed to be set via Ticket::create() or Ticket::update().
     */
    protected $fillable = [
        'user_id',
        'assigned_to',
        'subject',
        'description',
        'attachment',
        'priority',
        'status',
    ];

    /**
     * The relationships that should always be loaded.
     */
    protected $with = [
        'creator',
        'assignedTo',
    ];

    // --- RELATIONSHIPS ---

    /**
     * Get the student/user who created (opened) the ticket.
     */
    public function creator(): BelongsTo
    {
        // Links the 'user_id' foreign key to the User model.
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the admin/user currently assigned to resolve the ticket. (Can be null)
     */
    public function assignedTo(): BelongsTo
    {
        // Links the 'assigned_to' foreign key to the User model.
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Get the list of comments associated with this ticket.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
