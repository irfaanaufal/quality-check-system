<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerimaBb extends Model
{
    protected $table = 'terima_bb';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_bahan', 'tgl_terima', 'kode_supplier', 'nama_supplier', 'id_jenis',
        'jenis_bahan', 'jumlah_karung', 'timbang_ke', 'tonase', 'jam_awal', 'tempat_simpan',
        'kemasan_pakai', 'penggunaan_palet', 'kadar_air', 'kadar_broken',
        'keterangan', 'ket_bagian', 'sorting', 'nopol', 'status', 'user_created', 'posttime',
        'last_action', 'user_finish', 'user_updated', 'user_approved', 'jam_akhir', 'updated_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'sorting' => 'integer',
        'tgl_terima' => 'date',
        'posttime' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
