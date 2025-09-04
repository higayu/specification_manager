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
        Schema::create('change_requests', function (Blueprint $table) {
            $table->id();

            // 要件との紐付け
            $table->foreignId('requirement_id')->constrained()->cascadeOnDelete();

            // 旧・新バージョン
            $table->foreignId('old_version_id')->constrained('spec_versions');
            $table->foreignId('new_version_id')->nullable()->constrained('spec_versions');

            // ステータス
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            // 承認情報
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('change_requests');
    }
};
