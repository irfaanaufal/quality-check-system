<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KriteriaBb extends Model
{
    protected $table = 'kriteria_bb';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kriteria', 'varietas', 'jenis', 'nilai', 'status', 'posttime',
        'user_created', 'update_time', 'user_updated'
    ];

    protected $casts = [
        'id' => 'integer',
    ];
}
