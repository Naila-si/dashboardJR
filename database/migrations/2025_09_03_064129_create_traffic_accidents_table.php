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
        $table->integer('tahun_laka');
        $table->integer('bulan_laka');
        $table->date('tanggal_laka')->nullable();
        $table->string('kota');
        $table->string('kecamatan')->nullable();
        $table->string('lokasi')->nullable();
        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();
        $table->integer('korban_total')->default(0);
        $table->integer('korban_md')->default(0); // meninggal dunia
        $table->integer('ahli_waris_total')->default(0);
        $table->bigInteger('santunan')->default(0);
        $table->string('waktu')->nullable();
        $table->text('action_plan')->nullable();
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
