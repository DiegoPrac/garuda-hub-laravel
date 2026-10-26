<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            $table->foreignId('developer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('durasi');
            $table->text('proposal');

            $table->boolean('is_winner')->default(false);

            $table->timestamps();

            $table->unique(['project_id', 'developer_id']);
        });

        DB::statement(
            'ALTER TABLE bids ADD CONSTRAINT chk_bids_nilai
             CHECK (harga > 0 AND durasi > 0)'
        );
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
