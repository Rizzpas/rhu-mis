<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Prunable;

class Announcement extends Model
{
    use HasFactory, SoftDeletes, Prunable;

    protected $fillable = ['title', 'subheading', 'event_date', 'start_time', 'content', 'image_path', 'status', 'display_type', 'display_mode'];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'datetime:H:i',
    ];

    public function images()
    {
        return $this->hasMany(AnnouncementImage::class);
    }

    /**
     * Get the prunable model query.
     */
    public function prunable()
    {
        return static::onlyTrashed()->where('deleted_at', '<=', now()->subMonths(6));
    }
}
