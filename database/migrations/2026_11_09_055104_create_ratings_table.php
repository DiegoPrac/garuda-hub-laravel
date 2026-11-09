<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('client_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->foreignId('developer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedTinyInteger('nilai');
            $table->text('komentar')->nullable();

            $table->timestamps();

            // Satu proyek hanya boleh memiliki satu rating
            $table->unique('project_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
