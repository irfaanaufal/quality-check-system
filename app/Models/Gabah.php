<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Gabah extends Model
{
    protected $table = 'b_gabah';
    protected $primaryKey = 'id';
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
        'id' => 'integer',
        'berat' => 'integer',
        'stok' => 'integer',
        'poles' => 'integer',
        'sorter' => 'integer',
        'pecah_kulit' => 'integer',
        'tanggal' => 'date',
        'posttime_harga' => 'datetime',
        'harga' => 'integer',
        'harga_rata' => 'integer',
    ];

    public function reportTimbangGabah(): HasMany
    {
        return $this->hasMany(ReportTimbangGabah::class, 'no_penerimaan', 'no_penerimaan');
    }
}
