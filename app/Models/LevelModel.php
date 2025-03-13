<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LevelModel extends Model
{
    protected $table = 'm_level';
    protected $primaryKey = 'level_id';
    
    protected $fillable =['level_id', 'level_kode', 'level_nama'];
}