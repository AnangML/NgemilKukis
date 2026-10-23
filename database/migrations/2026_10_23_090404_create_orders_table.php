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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            $table->text('customer_address');
            $table->string('customer_city')->nullable();
            $table->string('shipping_method')->default('instant_grab');
            $table->unsignedInteger('shipping_cost')->default(0);
            $table->unsignedInteger('subtotal');
            $table->unsignedInteger('total_amount');
            $table->string('status')->default('pending');
            $table->string('payment_method')->default('xendit_qris');
            $table->string('xendit_invoice_id')->nullable();
            $table->string('xendit_invoice_url')->nullable();
            $table->boolean('is_gift')->default(false);
            $table->string('gift_recipient_name')->nullable();
            $table->string('gift_sender_name')->nullable();
            $table->foreignId('gift_card_template_id')->nullable()->constrained('greeting_card_templates')->nullOnDelete();
            $table->text('gift_message')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
