<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'api'
    ];

    public function text(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Text::class);
    }
}
