<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AttendanceCorrectRequest extends Model
{
    use HasFactory;

    // 保存を許可するカラムを追加
    protected $fillable = [
        'user_id',
        'attendance_id',
        'updated_check_in',
        'updated_check_out',
        'updated_break_times',
        'request_note',
        'status',
    ];

    // 【重要】配列（休憩時間）を自動的にJSONとして扱う設定
    protected $casts = [
        'updated_break_times' => 'json',
    ];

    // リレーション：どの勤怠に対する申請か
    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    // リレーション：誰が申請したか
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}