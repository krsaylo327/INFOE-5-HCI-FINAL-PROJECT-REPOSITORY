<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users
        $student = User::where('role', 'student')->first();
        $faculty = User::where('role', 'faculty')->first();
        $admin = User::where('role', 'admin')->first();

        if (!$student || !$faculty || !$admin) {
            $this->command->error('Please make sure you have at least one student, faculty, and admin user');
            return;
        }

        // Create tickets
        $ticket1 = Ticket::create([
            'user_id' => $student->id,
            'subject' => 'Cannot access lab computers',
            'description' => 'I tried to log in to the lab computers this morning but the system is not accepting my credentials. I have reset my password twice but still facing the same issue.',
            'priority' => 'High',
            'status' => 'Open',
        ]);

        $ticket2 = Ticket::create([
            'user_id' => $student->id,
            'subject' => 'Projector not working in Room 301',
            'description' => 'The projector in Room 301 is not turning on. I have an important presentation tomorrow.',
            'priority' => 'Medium',
            'status' => 'In Progress',
            'assigned_to' => $faculty->id,
        ]);

        Comment::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $faculty->id,
            'content' => 'I have checked the projector. The bulb needs to be replaced. Will order a new one today.',
        ]);

        Comment::create([
            'ticket_id' => $ticket2->id,
            'user_id' => $student->id,
            'content' => 'Thank you! When do you think it will be ready?',
        ]);

        $ticket3 = Ticket::create([
            'user_id' => $faculty->id,
            'subject' => 'Need additional software licenses',
            'description' => 'We need 5 more licenses for Adobe Creative Cloud for the graphic design class next semester.',
            'priority' => 'Low',
            'status' => 'Open',
        ]);

        $ticket4 = Ticket::create([
            'user_id' => $student->id,
            'subject' => 'Wi-Fi connection dropping frequently',
            'description' => 'The Wi-Fi connection in the library keeps dropping every 10-15 minutes. This is affecting my online classes.',
            'priority' => 'High',
            'status' => 'Resolved',
            'assigned_to' => $admin->id,
        ]);

        Comment::create([
            'ticket_id' => $ticket4->id,
            'user_id' => $admin->id,
            'content' => 'We have identified the issue with the router and replaced it. Please check if the connection is stable now.',
        ]);

        Comment::create([
            'ticket_id' => $ticket4->id,
            'user_id' => $student->id,
            'content' => 'Yes! The connection is much better now. Thank you!',
        ]);

        $this->command->info('Created ' . Ticket::count() . ' tickets with comments!');
    }
}

