<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('bullet_test_case_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order_no')->default(1);
            $table->string('title'); // 例: "1. 初期表示"
            $table->longText('source_text')->nullable(); // 元の箇条書きテキスト（再パース用）
            $table->timestamps();
        });

        Schema::create('bullet_test_case_rows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->constrained('bullet_test_case_groups')->cascadeOnDelete();
            $table->unsignedInteger('order_no')->default(1);
            $table->string('no')->nullable();          // 例: "TC1-1"
            $table->string('feature');                 // 機能
            $table->string('input_condition')->nullable(); // 入力条件
            $table->text('expected');                  // 期待結果（HTML可）
            $table->boolean('is_done')->default(false); // 完了フラグ
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('bullet_test_case_rows');
        Schema::dropIfExists('bullet_test_case_groups');
    }
};
