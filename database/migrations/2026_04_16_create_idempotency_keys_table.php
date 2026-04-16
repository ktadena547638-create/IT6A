<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Create idempotency keys table for deduplication
     * ✅ HARDENED: Prevents duplicate POST requests from creating duplicate records
     * 
     * Idempotency works by:
     * 1. Client sends Idempotency-Key header on POST request
     * 2. Server checks if this key exists in idempotency_keys table
     * 3. If exists and not expired (24 hours), return cached response
     * 4. If new, execute request and cache response
     * 5. If expired, generate new response and update cache
     */
    public function up(): void
    {
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();
            $table->string('idempotency_key', 255)->unique('idempotency_keys_unique');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('request_method')->default('POST'); // POST, PUT, PATCH
            $table->string('request_path', 500);
            $table->json('request_hash'); // Hash of request body for validation
            $table->json('response_data')->nullable(); // Cached response
            $table->integer('response_status')->nullable(); // HTTP status code
            $table->timestamp('expires_at')->nullable(); // 24-hour TTL
            $table->timestamps();

            // Indexes for efficient lookups
            $table->index(['idempotency_key', 'user_id']);
            $table->index(['user_id', 'request_path']);
            $table->index('expires_at'); // For cleanup queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('idempotency_keys');
    }
};
