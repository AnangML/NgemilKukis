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
        // 1. Create coupons table
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique()->index();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 20)->default('fixed'); // 'fixed' (nominal Rp) or 'percentage' (%)
            $table->unsignedInteger('value'); // e.g. 15 for 15% or 20000 for Rp 20.000
            $table->unsignedInteger('min_order_amount')->default(0);
            $table->unsignedInteger('max_discount_amount')->nullable();
            $table->unsignedInteger('usage_limit_total')->nullable();
            $table->unsignedInteger('usage_limit_per_user')->default(1);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('is_stackable_with_member')->default(true);
            $table->timestamps();
        });

        // 2. Create coupon usages audit table
        Schema::create('coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('coupon_id')->constrained('coupons')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('customer_email')->nullable();
            $table->string('customer_phone')->nullable();
            $table->unsignedInteger('discount_amount');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['coupon_id', 'user_id']);
            $table->index(['coupon_id', 'customer_phone']);
        });

        // 3. Create point transactions ledger table
        Schema::create('point_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('type', 30); // 'earned', 'redeemed', 'bonus', 'adjustment'
            $table->integer('amount'); // +100 (credit) or -50 (debit)
            $table->unsignedInteger('balance_after');
            $table->string('description');
            $table->timestamp('created_at')->useCurrent()->index();
        });

        // 4. Add points_balance column to users table
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('points_balance')->default(0)->after('member_discount_amount');
        });

        // 5. Add coupon & points columns to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('coupon_id')->nullable()->after('discount_amount')->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code', 50)->nullable()->after('coupon_id');
            $table->unsignedInteger('coupon_discount_amount')->default(0)->after('coupon_code');
            $table->unsignedInteger('points_redeemed')->default(0)->after('coupon_discount_amount');
            $table->unsignedInteger('points_discount_amount')->default(0)->after('points_redeemed');
            $table->unsignedInteger('points_earned')->default(0)->after('points_discount_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('coupon_id');
            $table->dropColumn([
                'coupon_code',
                'coupon_discount_amount',
                'points_redeemed',
                'points_discount_amount',
                'points_earned',
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('points_balance');
        });

        Schema::dropIfExists('point_transactions');
        Schema::dropIfExists('coupon_usages');
        Schema::dropIfExists('coupons');
    }
};
