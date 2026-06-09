<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    protected $fillable = ['user_id', 'date', 'clock_in', 'clock_out', 'comment'];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceBreak ()
    {
        return $this->hasMany(AttendanceBreak::class);
    }

    public function activeBreak()
    {
        return $this->hasOne(AttendanceBreak::class)->whereNull('break_out');
    }

    public function attendanceCorrectRequest()
    {
        return $this->hasMany(AttendanceCorrectRequest::class);
    }

    public function getStatusAttribute(): string
    {
        if ($this->clock_out) return '退勤済';
        if ($this->activeBreak) return '休憩中';

        return '出勤中';
    }
}
