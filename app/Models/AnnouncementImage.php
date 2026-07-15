<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AnnouncementImage extends Model
{
    protected $fillable = ['announcement_id', 'image_path', 'content', 'sort_order', 'layout', 'type', 'media_type', 'video_url'];

    protected $touches = ['announcement'];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
