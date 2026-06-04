<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VarietasBeras extends Model
{
    protected $table = 'varietas_beras';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'nama_varietas', 'jenis', 'alias', 'stok', 'minimum_tonase', 'status',
        'user_created', 'created_at', 'user_updated', 'updated_at', 'posttime', 'update_stok_at'
    ];

    protected $casts = [
        'id' => 'integer',
        'status' => 'integer',
    ];
}
