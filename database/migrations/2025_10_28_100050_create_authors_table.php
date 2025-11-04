<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('order')->default(1);
            $table->boolean('is_corresponding')->default(false);
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('authors');
    }
};