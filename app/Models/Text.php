<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Text extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'texts';

    protected $fillable = [
        'text',
        'folder_id',
    ];

    protected $appends = [
        'id',
    ];
    public function getIdAttribute($value = null)
    {
        return $this->attributes['_id'];
    }

//    public function folder()
//    {
//        return $this->belongsTo(Folder::class);
//    }
}
