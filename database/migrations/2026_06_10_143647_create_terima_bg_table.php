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
        Schema::create('terima_bg', function (Blueprint $table) {
            $table->id();
            $table->string('kode_supplier');
            $table->string('nama_supplier');
            $table->date('tgl_terima');
            $table->unsignedBigInteger('id_jenis');
            $table->string('jenis_bahan');
            $table->string('tonase')->nullable();
            $table->string('nopol')->nullable();
            $table->string('jam_awal');
            $table->string('jam_akhir')->nullable();
            $table->string('tempat_simpan')->nullable();
            $table->string('penggunaan_palet')->nullable();
            $table->string('status')->default('Proses');
            $table->text('keterangan')->nullable();
            $table->dateTime('posttime')->nullable();
            $table->string('user_created')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('user_updated')->nullable();
            $table->string('last_action')->nullable();
            $table->string('user_finish')->nullable();
            $table->string('user_approved')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terima_bg');
    }
};
