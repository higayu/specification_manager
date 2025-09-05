<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('specifications', function (Blueprint $t) {
      $t->id();
      $t->foreignId('project_id')->constrained()->cascadeOnDelete();
      $t->string('code', 64);                      // ex) REQ-001（プロジェクト内ユニーク）
      $t->string('title');
      $t->string('status', 32)->default('draft');  // draft/approved/deprecated
      $t->unsignedBigInteger('current_version_id')->nullable();
      $t->timestamps();
      $t->unique(['project_id','code']);
    });

    Schema::create('specification_versions', function (Blueprint $t) {
      $t->id();
      $t->foreignId('specification_id')->constrained('specifications')->cascadeOnDelete();
      $t->unsignedInteger('version_no');
      $t->longText('body_md');
      $t->json('attributes')->nullable();
      $t->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamps();
      $t->unique(['specification_id','version_no']);
    });

    Schema::table('specifications', function (Blueprint $t) {
      $t->foreign('current_version_id')
        ->references('id')->on('specification_versions')->nullOnDelete();
    });

    Schema::create('spec_change_requests', function (Blueprint $t) {
      $t->id();
      $t->foreignId('project_id')->constrained()->cascadeOnDelete();
      $t->foreignId('specification_id')->constrained('specifications')->cascadeOnDelete();
      $t->unsignedBigInteger('from_version_id')->nullable();
      $t->unsignedBigInteger('to_version_id')->nullable();
      $t->string('reason');
      $t->text('impact')->nullable();
      $t->string('status', 32)->default('proposed'); // proposed/approved/rejected/implemented
      $t->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
      $t->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
      $t->timestamps();
      $t->foreign('from_version_id')->references('id')->on('specification_versions')->nullOnDelete();
      $t->foreign('to_version_id')->references('id')->on('specification_versions')->nullOnDelete();
    });

    // （任意）bullet_* と連動させるならこちらを後で有効化
    // Schema::table('bullet_test_case_groups', function (Blueprint $t) {
    //   $t->foreignId('specification_id')->nullable()->constrained('specifications')->nullOnDelete()->after('project_id');
    // });
  }

  public function down(): void {
    Schema::dropIfExists('spec_change_requests');
    Schema::table('specifications', fn(Blueprint $t)=>$t->dropForeign(['current_version_id']));
    Schema::dropIfExists('specification_versions');
    Schema::dropIfExists('specifications');
    // Schema::table('bullet_test_case_groups', fn(Blueprint $t)=>$t->dropConstrainedForeignId('specification_id'));
  }
};
