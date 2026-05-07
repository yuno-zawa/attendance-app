<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Attendance extends Model
{
    use HasFactory;

    protected $casts = [
    'check_in' => 'datetime',
    'check_out' => 'datetime',
    ];

    protected $fillable = ['user_id', 'check_in', 'check_out','note'];

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function correctRequest()
    {
        return $this->hasOne(AttendanceCorrectRequest::class);
    }
}
