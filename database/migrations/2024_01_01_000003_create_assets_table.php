<?php
// ============================================================
// Migration: create_assets_table
// ============================================================

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number', 100)->unique();
            $table->string('asset_tag', 100)->nullable();
            $table->foreignId('category_id')->constrained()->restrictOnDelete();
            $table->foreignId('brand_id')->constrained()->restrictOnDelete();
            $table->enum('status', ['Available', 'Deployed', 'Spare', 'Defective'])->default('Available');
            $table->date('date_added');
            $table->timestamp('removed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'date_added']);
            $table->index('category_id');
            $table->index('brand_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
