
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

            // Relasi penawaran dengan proyek
            $table->foreignId('project_id')
                ->constrained('projects')
                ->cascadeOnDelete();

            // Relasi penawaran dengan developer
            $table->foreignId('developer_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Informasi penawaran
            $table->unsignedBigInteger('harga');
            $table->unsignedInteger('durasi');
            $table->text('proposal');

            // Penanda pemenang lelang
            $table->boolean('is_winner')->default(false);

            $table->timestamps();

            // Satu developer hanya boleh menawar sekali
            // pada proyek yang sama
            $table->unique(['project_id', 'developer_id']);
        });

// CHECK constraint untuk MySQL dan PostgreSQL.
// SQLite tetap menggunakan validasi aplikasi.

if (in_array(DB::getDriverName(), ['mysql', 'pgsql'], true)) {

            DB::statement(
                'ALTER TABLE bids ADD CONSTRAINT chk_bids_nilai
                 CHECK (harga > 0 AND durasi > 0)'
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
