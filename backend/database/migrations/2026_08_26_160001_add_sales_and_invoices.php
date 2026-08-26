<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_method', 40)->nullable()->after('shipping_address');
            $table->string('channel', 20)->default('ONLINE')->after('payment_method');
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('document_number')->nullable()->after('reason');
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->string('type', 20);
            $table->foreignId('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_document')->nullable();
            $table->string('payment_method', 40)->nullable();
            $table->decimal('total', 10, 2);
            $table->json('items');
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropColumn('document_number');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_method', 'channel']);
        });
    }
};
