<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectRequest extends Model
{
    protected $fillable = ['attendance_id', 'request_note', 'status'];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }
}
