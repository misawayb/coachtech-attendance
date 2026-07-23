<?php

namespace App\Http\Controllers;

use App\Enums\CorrectRequestStatus;
use App\Models\AttendanceCorrectBreak;
use App\Models\AttendanceCorrectRequest;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;

class StampCorrectionRequestController extends Controller
{
    public function store(Request $request, string $date)
    {
        $user = auth()->user();

        $targetRecord = AttendanceRecord::where('user_id', $user->id)
            ->where('date', $date)
            ->firstOrFail();

        $correctRequest = AttendanceCorrectRequest::create([
            'attendance_record_id' => $targetRecord->id,
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'comment' => $request->comment,
            'status' => CorrectRequestStatus::Pending->value,
        ]);

        foreach ($request->breaks ?? [] as $break) {
            if (empty($break['break_in']) && empty($break['break_out'])) {
                continue;
            }

            AttendanceCorrectBreak::create([
                'attendance_correct_request_id' => $correctRequest->id,
                'break_in' => $break['break_in'] ?? null,
                'break_out' => $break['break_out'] ?? null,
            ]);
        }

        return redirect()->route('attendance.edit', ['date' => $date])
            ->with('message', '修正申請を送信しました');
    }
}
