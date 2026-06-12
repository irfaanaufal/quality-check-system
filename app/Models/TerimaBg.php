<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TerimaBg extends Model
{
    protected $table = 'terima_bg';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kode_supplier', 'nama_supplier', 'tgl_terima', 'id_jenis', 'jenis_bahan', 'tonase', 
        'nopol', 'jam_awal', 'jam_akhir', 'tempat_simpan', 'penggunaan_palet', 'status', 
        'keterangan', 'posttime', 'user_created', 'updated_at', 'user_updated', 'last_action', 
        'user_finish', 'user_approved'
    ];

    protected $casts = [
        'id' => 'integer',
        'tgl_terima' => 'date',
        'posttime' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
