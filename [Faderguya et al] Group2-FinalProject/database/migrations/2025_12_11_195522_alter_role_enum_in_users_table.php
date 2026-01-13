<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // MySQL requires dropping the column first to change the ENUM values.
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        // Re-add the role column with the new 'faculty' value
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'admin', 'faculty']) // <-- NEW VALUES HERE
            ->default('student')
                ->after('email');
        });

        // IMPORTANT: If you have existing users, you need to re-set their role
        // as the dropColumn/re-add process will assign the default ('student') to everyone.
        // You may need to run manual SQL here if you had existing admins.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert the ENUM back to the original definition
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['student', 'admin'])
                ->default('student')
                ->after('email');
        });
    }
};
