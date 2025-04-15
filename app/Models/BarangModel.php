<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangModel extends Model
{
    use HasFactory;

    protected $table = 'm_barang';
    protected $primaryKey = 'barang_id'; // Sesuai dengan struktur database
    protected $fillable = [
        'kategori_id',  
        'barang_kode',
        'barang_nama',
        'harga_beli',
        'harga_jual'
    ];
    
    public $timestamps = false; // Karena kolom created_at & updated_at ada tapi tanpa default

    public function kategori()
    {
        return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
    }
}