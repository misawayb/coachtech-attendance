<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRecord;
use App\Enums\CorrectRequestStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function index()
    {
        return redirect()->route('attendance.create');
    }

    public function create()
    {
        $user = auth()->user();
        $today = now()->isoFormat('YYYY年M月D日(ddd)');
        $time = now()->format('H:i');
        $todayRecord = AttendanceRecord::where('user_id', $user->id)
            ->where('date', today()->toDateString())
            ->first();

        if ($todayRecord === null) {
            $status = '勤務外';
        } else {
            $status = $todayRecord->status;
        }

        return view('attendance.stamp', compact('user', 'today', 'time', 'status', 'todayRecord'));
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $todayRecord = AttendanceRecord::where('user_id', $user->id)
            ->where('date', today()->toDateString())
            ->first();
        if ($todayRecord === null) {
            $status = '勤務外';
        } else {
            $status = $todayRecord->status;
        }

        if ($status === '勤務外') {
            AttendanceRecord::create([
                'user_id' => $user->id,
                'date' => today()->toDateString(),
                'clock_in' => now()->format('H:i'),
            ]);
        } elseif ($status === '出勤中') {
            if ($request->action === 'clock_out') {
                $todayRecord->update(['clock_out' => now()->format('H:i')]);
            } elseif ($request->action === 'break_in') {
                AttendanceBreak::create([
                    'attendance_record_id' => $todayRecord->id,
                    'break_in' => now()->format('H:i')
                ]);
            }
        } elseif ($status === '休憩中') {
            $todayRecord->activeBreak->update(['break_out' => now()->format('H:i')]);
        }

        return redirect('/attendance');
    }

    public function show(Request $request)
    {
        $targetMonth = Carbon::parse($request->query('month'));

        $user = auth()->user();
        $period = CarbonPeriod::create($targetMonth->copy()->startOfMonth(), $targetMonth->copy()->endOfMonth());

        $targetMonthAttendance = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('date', [$targetMonth->copy()->startOfMonth()->toDateString(), $targetMonth->copy()->endOfMonth()->toDateString()])
            ->get();

        $targetMonthAttendanceByDate = $targetMonthAttendance->keyBy('date');

        $attendanceList = [];
        foreach ($period as $day) {
            $record = $targetMonthAttendanceByDate->get($day->toDateString());

            $displayDate = $day->isoFormat('MM/DD(ddd)');

            $attendanceList[] = [
                'date' => $displayDate,
                'clockIn' => $record && $record->clock_in ? Carbon::parse($record->clock_in)->format('H:i') : '',
                'clockOut' => $record && $record->clock_out ? Carbon::parse($record->clock_out)->format('H:i') : '',
                'breakTime' => $record ? $record->break_time : '',
                'workTime' => $record ? $record->work_time : '',
                'editUrl' => route('attendance.edit', ['date' => $day->toDateString()]),
            ];
        }

        return view('attendance.list', compact('attendanceList', 'targetMonth'));
    }

    public function edit(string $date)
    {
        $user = auth()->user();
        $targetDate = Carbon::parse($date);
        $displayDate = $targetDate->isoFormat('MM/DD(ddd)');

        if ($targetDate->isFuture()) {
            return redirect()->route('attendance.show')->with('error', '指定の日付は未来であるため勤怠の詳細は表示できません');
        }

        $targetRecord = AttendanceRecord::firstOrCreate([
            'user_id' => $user->id,
            'date' => $targetDate->toDateString(),
        ]);

        $pendingRequest = $targetRecord->attendanceCorrectRequest()
            ->with('attendanceCorrectBreak')
            ->where('status', CorrectRequestStatus::Pending->value)
            ->first();

        $isPending = $pendingRequest !== null;

        if ($isPending) {
            $displayClockIn = $pendingRequest->clock_in;
            $displayClockOut = $pendingRequest->clock_out;
            $displayBreaks = $pendingRequest->attendanceCorrectBreak;
            $displayComment = $pendingRequest->comment;
        } else {
            $displayClockIn = $targetRecord->clock_in;
            $displayClockOut = $targetRecord->clock_out;
            $displayBreaks = $targetRecord->attendanceBreak;
            $displayComment = $targetRecord->comment;
        }

        $breaksInput = old('breaks', $displayBreaks->map(fn($break) => [
            'break_in' => $break->break_in ? Carbon::parse($break->break_in)->format('H:i') : '',
            'break_out' => $break->break_out ? Carbon::parse($break->break_out)->format('H:i') : '',
        ])->toArray());

        if (!$isPending && !old('breaks')) {
            $breaksInput[] = ['break_in' => '', 'break_out' => ''];
        }

        return view('attendance.detail', compact(
            'targetDate',
            'targetRecord',
            'displayDate',
            'isPending',
            'displayClockIn',
            'displayClockOut',
            'displayBreaks',
            'displayComment',
            'breaksInput'
        ));
    }
}

