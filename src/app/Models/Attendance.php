<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['user_id', 'check_in', 'check_out'];

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
