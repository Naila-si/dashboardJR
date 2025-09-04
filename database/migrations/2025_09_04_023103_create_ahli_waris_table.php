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
        Schema::create('ahliwaris', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->string('no_berkas');
            $table->enum('cedera', ['LL', 'MD']);
            $table->string('nama_pemohon');
            $table->text('alamat');
            $table->string('penyelesaian')->nullable();
            $table->enum('status', ['Proses', 'Selesai', 'Rejected']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahli_waris');
    }
};
