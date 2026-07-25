<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Enums\CorrectRequestStatus;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', CorrectRequestStatus::Pending->value);

        $correctRequests = AttendanceCorrectRequest::with(['attendanceRecord.user'])
            ->where('status', $status)
            ->get()
            ->sortBy(fn($correctRequest) => $correctRequest->attendanceRecord->date);

        $requestList = [];
        foreach ($correctRequests as $correctRequest) {
            $requestList[] = [
                'statusLabel' => $correctRequest->status === CorrectRequestStatus::Pending->value ? '承認待ち' : '承認済み',
                'userName' => $correctRequest->attendanceRecord->user->name,
                'targetDate' => Carbon::parse($correctRequest->attendanceRecord->date)->format('Y/m/d'),
                'comment' => $correctRequest->comment,
                'requestedAt' => $correctRequest->created_at->format('Y/m/d'),
                'detailUrl' => route('correction.show', ['attendance_correct_request_id' => $correctRequest->id]),
            ];
        }

        return view('admin.stamp_correction_request.index', compact('requestList', 'status'));
    }

    public function show($attendance_correct_request_id)
    {
        $correctRequest = AttendanceCorrectRequest::with(['attendanceRecord.user', 'attendanceCorrectBreak'])
            ->findOrFail($attendance_correct_request_id);

        return view('admin.stamp_correction_request.show', compact('correctRequest'));
    }

    public function store($attendance_correct_request_id)
    {
        $correctRequest = AttendanceCorrectRequest::with('attendanceCorrectBreak')->findOrFail($attendance_correct_request_id);
        $targetRecord = $correctRequest->attendanceRecord;

        $targetRecord->update([
            'clock_in' => $correctRequest->clock_in,
            'clock_out' => $correctRequest->clock_out,
            'comment' => $correctRequest->comment,
        ]);

        $targetRecord->attendanceBreak()->delete();

        foreach ($correctRequest->attendanceCorrectBreak as $break) {
            $targetRecord->attendanceBreak()->create([
                'break_in' => $break->break_in,
                'break_out' => $break->break_out,
            ]);
        }

        $correctRequest->update(['status' => CorrectRequestStatus::Approved->value]);

        return redirect()->route('correction.show', ['attendance_correct_request_id' => $attendance_correct_request_id])
            ->with('message', '承認しました');
    }
}
