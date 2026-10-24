<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->unique()->after('email');
            $table->string('google_id')->nullable()->unique()->after('phone');
            $table->string('avatar_url')->nullable()->after('google_id');
            $table->string('auth_provider')->default('email')->after('avatar_url');
            $table->text('saved_address')->nullable()->after('auth_provider');
            $table->string('saved_city')->nullable()->after('saved_address');
            $table->unsignedInteger('member_discount_amount')->default(10000)->after('saved_city');
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'google_id',
                'avatar_url',
                'auth_provider',
                'saved_address',
                'saved_city',
                'member_discount_amount',
                'phone_verified_at',
            ]);
        });
    }
};
