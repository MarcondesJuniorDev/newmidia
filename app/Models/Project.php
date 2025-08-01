<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'title',
        'summary',
        'status',
    ];

    public function packages()
    {
        return $this->hasMany(Package::class);
    }
}
