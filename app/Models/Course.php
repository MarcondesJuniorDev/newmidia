<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'image',
        'project_id',
        'category_id',
        'title',
        'summary',
        'description',
        'status',
        'is_featured'
    ];

    public function category()
    {
        return $this->belongsTo(CourseCategory::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
