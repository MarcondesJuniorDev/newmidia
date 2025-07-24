<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    public function contents()
    {
        return $this->hasMany(Content::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($package) {
            if ($package->isDirty('image')) {
                if ($package->getOriginal('image')) {
                    Storage::disk('public')->delete($package->getOriginal('image'));
                }
            }
        });

        static::deleting(function ($package) {
            if ($package->image) {
                Storage::disk('public')->delete($package->image);
            }
        });
    }
}
