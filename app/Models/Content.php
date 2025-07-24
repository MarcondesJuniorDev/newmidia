<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($content) {
            if ($content->isDirty('file')) {
                if ($content->getOriginal('file')) {
                    Storage::disk('public')->delete($content->getOriginal('file'));
                }
            }
        });

        static::deleting(function ($content) {
            if ($content->file) {
                Storage::disk('public')->delete($content->file);
            }
        });
    }
}
