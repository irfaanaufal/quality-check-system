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
        Schema::create('report_timbang_gabah', function (Blueprint $table) {
            $table->id();
            $table->string('id_bahan');
            $table->string('no_penerimaan')->nullable();
            $table->date('tanggal_terima')->nullable();
            $table->string('supplier')->nullable();
            $table->unsignedBigInteger('id_jenis');
            $table->string('jumlah_karung')->nullable();
            $table->string('timbang_ke')->nullable();
            $table->string('tonase')->nullable();
            $table->string('kadar_air')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('ket_bagian')->nullable();
            $table->integer('sorting')->default(0);
            $table->string('user_created')->nullable();
            $table->dateTime('posttime')->nullable();
            $table->string('user_updated')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('last_action')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_timbang_gabah');
    }
};
