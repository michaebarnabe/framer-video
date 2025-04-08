<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Frame extends Model
{
    use HasFactory;

    protected $fillable = [
        'conversion_id',
        'filename',
        'frame_number',
        'file_path',
    ];

    public function conversion()
    {
        return $this->belongsTo(Conversion::class);
    }
}