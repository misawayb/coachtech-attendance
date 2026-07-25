<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectRequest extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_record_id', 'clock_in', 'clock_out', 'comment', 'status', 'approved_by'];

    public function user ()
    {
        return $this->belongsTo(User::class);
    }

    public function attendanceRecord()
    {
        return $this->belongsTo(AttendanceRecord::class);
    }

    public function attendanceCorrectBreak()
    {
        return $this->hasMany(AttendanceCorrectBreak::class);
    }
}
