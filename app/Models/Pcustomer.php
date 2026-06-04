<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pcustomer extends Model
{
    protected $table = 'pcustomer';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'kode_principal', 'kode_cust', 'nama_cust', 'alamat', 'no_telp', 'no_fax',
        'nama_pic', 'no_contact', 'wilayah', 'sales'
    ];

    protected $casts = [
        'id' => 'integer',
        'wilayah' => 'integer',
        'sales' => 'integer',
    ];
}
