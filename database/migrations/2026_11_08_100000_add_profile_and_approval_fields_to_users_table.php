<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // nullable() keeps this migration valid on SQLite as well as MySQL
            $table->string('username')->nullable()->unique()->after('name');
            $table->string('phone')->nullable();
            $table->string('company')->nullable();
            $table->text('address')->nullable();
            $table->boolean('is_admin')->default(false);
            // pending = registered, waiting for admin approval
            // active  = may log in
            // suspended = declined or switched off by an admin
            $table->string('status')->default('pending')->index();
            $table->timestamp('approved_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropIndex(['status']);
            $table->dropColumn(['username', 'phone', 'company', 'address', 'is_admin', 'status', 'approved_at']);
        });
    }
};
