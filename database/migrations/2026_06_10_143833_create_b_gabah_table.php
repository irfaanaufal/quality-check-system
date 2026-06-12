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
        if (!Schema::hasTable('b_gabah')) {
            Schema::create('b_gabah', function (Blueprint $table) {
                $table->bigIncrements('idb_gabah');
                $table->string('no_penerimaan')->nullable();
                $table->string('no_sample')->nullable();
                $table->date('tanggal')->nullable();
                $table->string('kode_principal')->nullable();
                $table->string('supplier')->nullable();
                $table->unsignedBigInteger('id_jenis')->nullable();
                $table->string('jenis')->nullable();
                $table->integer('berat')->default(0);
                $table->integer('stok')->default(0);
                $table->string('kemasan')->nullable();
                $table->string('kondisi')->nullable();
                $table->string('kendaraan')->nullable();
                $table->string('keputusan')->nullable();
                $table->string('nopol')->nullable();
                $table->string('status')->default('Proses');
                $table->string('user')->nullable();
                $table->text('keterangan')->nullable();
                $table->integer('poles')->default(0);
                $table->integer('sorter')->default(0);
                $table->integer('pecah_kulit')->default(0);
                $table->string('penggunaan_palet')->nullable();
                $table->string('lokasi_penyimpanan')->nullable();
                $table->string('nilai')->nullable();
                $table->string('user_approve')->nullable();
                $table->integer('harga')->default(0);
                $table->integer('harga_rata')->default(0);
                $table->dateTime('posttime_harga')->nullable();
                $table->unsignedBigInteger('id_timbang')->nullable();
                $table->dateTime('posttime')->nullable();
                $table->string('warna')->nullable();
                $table->string('aroma')->nullable();
                $table->string('indikasi_kimia')->nullable();
                $table->string('catatan_cek')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b_gabah');
    }
};
