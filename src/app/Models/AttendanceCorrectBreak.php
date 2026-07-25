<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceCorrectBreak extends Model
{
    use HasFactory;

    protected $fillable = ['attendance_correct_request_id', 'break_in', 'break_out'];

    public function attendanceCorrectRequest ()
    {
        return $this->belongsTo(AttendanceCorrectRequest::class);
    }
}
