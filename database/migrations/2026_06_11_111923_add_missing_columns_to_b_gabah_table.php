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
        Schema::table('b_gabah', function (Blueprint $table) {
            if (!Schema::hasColumn('b_gabah', 'no_sample')) {
                $table->string('no_sample')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'kemasan')) {
                $table->string('kemasan')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'poles')) {
                $table->integer('poles')->default(0);
            }
            if (!Schema::hasColumn('b_gabah', 'sorter')) {
                $table->integer('sorter')->default(0);
            }
            if (!Schema::hasColumn('b_gabah', 'pecah_kulit')) {
                $table->integer('pecah_kulit')->default(0);
            }
            if (!Schema::hasColumn('b_gabah', 'harga_rata')) {
                $table->integer('harga_rata')->default(0);
            }
            if (!Schema::hasColumn('b_gabah', 'posttime_harga')) {
                $table->dateTime('posttime_harga')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'id_timbang')) {
                $table->unsignedBigInteger('id_timbang')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'posttime')) {
                $table->dateTime('posttime')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'aroma')) {
                $table->string('aroma')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'indikasi_kimia')) {
                $table->string('indikasi_kimia')->nullable();
            }
            if (!Schema::hasColumn('b_gabah', 'catatan_cek')) {
                $table->string('catatan_cek')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b_gabah', function (Blueprint $table) {
            $columns = [
                'no_sample', 'kemasan', 'poles', 'sorter', 'pecah_kulit',
                'harga_rata', 'posttime_harga', 'id_timbang', 'posttime',
                'aroma', 'indikasi_kimia', 'catatan_cek'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('b_gabah', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
