<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('traffic_accidents', function (Blueprint $table) {
            $table->id();
            $table->string('provinsi');
            $table->string('kota');
            $table->string('kecamatan');
            $table->integer('korban_md'); // Meninggal Dunia
            $table->integer('korban_ll'); // Luka-luka
            $table->integer('korban_total');
            $table->bigInteger('santunan');
            $table->integer('bulan_laka');
            $table->integer('tahun_laka');
            $table->string('rencana_penanganan');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traffic_accidents');
    }
};
