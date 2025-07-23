<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'code',
        'description',
        'tags',
        'image',
        'status',
        'type',
        'project_id',
    ];

    protected $casts = [
        'tags' => 'array',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
