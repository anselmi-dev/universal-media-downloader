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
        Schema::create('download_requests', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500)->index();
            $table->string('platform', 50)->nullable()->index();
            $table->string('status', 20)->index(); // success, no_media, error
            $table->text('error_message')->nullable();
            $table->unsignedInteger('items_count')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('site_host', 255)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_requests');
    }
};
