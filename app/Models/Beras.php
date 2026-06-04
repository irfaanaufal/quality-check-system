<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beras extends Model
{
    protected $table = 'b_beras';
    protected $primaryKey = 'idb_beras';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'no_penerimaan', 'no_sample', 'tanggal', 'kode_principal', 'supplier', 'id_jenis', 'jenis',
        'berat', 'stok', 'kemasan', 'kondisi', 'kendaraan', 'keputusan', 'nopol', 'status', 'user',
        'keterangan', 'poles', 'sorter', 'pecah_kulit', 'penggunaan_palet', 'lokasi_penyimpanan',
        'nilai', 'user_approve', 'harga', 'harga_rata', 'posttime_harga', 'id_timbang', 'posttime',
        'warna', 'aroma', 'indikasi_kimia', 'catatan_cek'
    ];

    protected $casts = [
        'idb_beras' => 'integer',
        'berat' => 'integer',
        'stok' => 'integer',
        'poles' => 'integer',
        'sorter' => 'integer',
        'pecah_kulit' => 'integer',
        'tanggal' => 'date',
    ];
}
