<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PractitionerSchedule extends Model
{
    protected $fillable = [
        'user_id',
        'day_of_week',
        'time_in',
        'time_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
