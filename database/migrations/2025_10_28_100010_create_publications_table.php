<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->nullable();
            $table->string('venue')->nullable();
            $table->string('doi')->nullable();
            $table->enum('status', ['drafting', 'submitted', 'revise', 'accepted', 'published'])->default('drafting');
            $table->date('target_deadline')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('publications');
    }
};