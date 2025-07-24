<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'file',
        'title',
        'author_id',
        'ownership_rights',
        'source_credit',
        'license_type',
        'tags',
        'status',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
