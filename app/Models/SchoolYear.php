<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    protected $fillable = [
        'year',
        'current'
    ];

    protected static function booted()
    {
        static::saving(function ($schoolYear) {
            if ($schoolYear->current) {
                static::where('id', '!=', $schoolYear->id)->update(['current' => false]);
            }
        });
    }
}
