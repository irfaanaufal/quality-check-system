<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportTimbangGabah extends Model
{
    protected $table = 'report_timbang_gabah';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'id_bahan', 'no_penerimaan', 'tanggal_terima', 'supplier', 'id_jenis', 
        'jumlah_karung', 'timbang_ke', 'tonase', 'kadar_air', 'keterangan', 
        'ket_bagian', 'sorting', 'user_created', 'posttime', 'user_updated', 
        'updated_at', 'last_action'
    ];

    protected $casts = [
        'id' => 'integer',
        'sorting' => 'integer',
        'tanggal_terima' => 'date',
        'posttime' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
