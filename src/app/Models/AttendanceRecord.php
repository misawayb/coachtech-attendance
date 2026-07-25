<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AttendanceRecord extends Model
{
    use HasFactory;

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

    // 休憩中または退勤済みの判定
    public function getStatusAttribute(): string
    {
        if ($this->clock_out) return '退勤済';
        if ($this->activeBreak) return '休憩中';

        return '出勤中';
    }

    // 休憩合計（分）
    public function getBreakMinutesAttribute(): int
    {
        return $this->attendanceBreak->sum(function ($break) {
            if (!$break->break_in || !$break->break_out) {
                return 0;
            }
            return Carbon::parse($break->break_in)->diffInMinutes($break->break_out);
        });
    }

    // 休憩合計（H:MM表示）
    public function getBreakTimeAttribute(): string
    {
        $minutes = $this->break_minutes;
        return $minutes > 0 ? sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60) : '';
    }

    // 実働時間（分）※出退勤どちらか欠けていればnull
    public function getWorkMinutesAttribute(): ?int
    {
        if (!$this->clock_in || !$this->clock_out) {
            return null;
        }
        return Carbon::parse($this->clock_in)->diffInMinutes($this->clock_out) - $this->break_minutes;
    }

    // 実働時間（H:MM表示）
    public function getWorkTimeAttribute(): string
    {
        $minutes = $this->work_minutes;
        return $minutes !== null ? sprintf('%d:%02d', intdiv($minutes, 60), $minutes % 60) : '';
    }
}
