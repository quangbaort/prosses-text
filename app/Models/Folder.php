<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $connection = 'mongodb';
    protected $collection = 'folders';

    protected $fillable = [
        'name', 'api',
    ];
    protected $appends = [
        'id',
    ];
    protected $hidden = [
        '_id',
    ];

    public function getIdAttribute($value = null)
    {
        return $this->attributes['_id'];
    }


    public function text()
    {
        return $this->hasMany(Text::class);
    }

}
