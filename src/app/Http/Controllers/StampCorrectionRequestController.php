<?php

namespace App\Http\Controllers;

use App\Enums\CorrectRequestStatus;
use App\Http\Requests\StampCorrectionRequest;
use App\Models\AttendanceCorrectBreak;
use App\Models\AttendanceCorrectRequest;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StampCorrectionRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $status = $request->query('status', CorrectRequestStatus::Pending->value);

        $correctRequests = AttendanceCorrectRequest::whereHas('attendanceRecord', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
            ->with('attendanceRecord')
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->get();

        $requestList = [];
        foreach ($correctRequests as $correctRequest) {
            $requestList[] = [
                'statusLabel' => $correctRequest->status === CorrectRequestStatus::Pending->value ? '承認待ち' : '承認済み',
                'userName' => $user->name,
                'targetDate' => Carbon::parse($correctRequest->attendanceRecord->date)->format('Y/m/d'),
                'comment' => $correctRequest->comment,
                'requestedAt' => $correctRequest->created_at->format('Y/m/d'),
                'detailUrl' => route('attendance.edit', ['date' => Carbon::parse($correctRequest->attendanceRecord->date)->format('Y-m-d')]),
            ];
        }

        return view('stamp_correction_request.index', compact('requestList', 'status'));
    }

    public function store(StampCorrectionRequest $request, string $date)
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
