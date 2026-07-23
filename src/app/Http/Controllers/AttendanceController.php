<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\AttendanceBreak;
use App\Models\AttendanceRecord;
use App\Enums\CorrectRequestStatus;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('attendance.stamp');
    }

    public function create()
    {
        $user = auth()->user();
        $today = now()->isoFormat('YYYY年M月D日(ddd)');
        $time = now()->format('H:i');
        $todayRecord = AttendanceRecord::where('user_id', $user->id)
            ->where('date', today())
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
            ->where('date', today())
            ->first();
        if ($todayRecord === null) {
            $status = '勤務外';
        } else {
            $status = $todayRecord->status;
        }

        if ($status === '勤務外') {
            AttendanceRecord::create([
                'user_id' => $user->id,
                'date' => today(),
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
            ->whereBetween('date', [$targetMonth->copy()->startOfMonth(), $targetMonth->copy()->endOfMonth()])
            ->get();

        $targetMonthAttendanceByDate = $targetMonthAttendance->keyBy('date');

        $attendanceList = [];
        foreach ($period as $day) {
            $record = $targetMonthAttendanceByDate->get($day->toDateString());

            $displayDate = $day->isoFormat('MM/DD(ddd)');

            if ($record) {
                $clockIn = $record->clock_in ? Carbon::parse($record->clock_in)->format('H:i') : '';
                $clockOut = $record->clock_out ? Carbon::parse($record->clock_out)->format('H:i') : '';
                $breakMinutes = $record->attendanceBreak->sum(function ($break) {
                    if (!$break->break_in || !$break->break_out) {
                        return 0;
                    }
                    return Carbon::parse($break->break_in)->diffInMinutes($break->break_out);
                });
                if ($record->clock_in && $record->clock_out) {

                    $workMinutes = Carbon::parse($record->clock_in)->diffInMinutes($record->clock_out) - $breakMinutes;
                } else {
                    $workMinutes = null;
                }
            } else {
                $clockIn = '';
                $clockOut = '';
                $breakMinutes = 0;
                $workMinutes = null;
            }

            $breakTime = $breakMinutes > 0 ? sprintf('%d:%02d', intdiv($breakMinutes, 60), $breakMinutes % 60) : '';
            $workTime = $workMinutes !== null ? sprintf('%d:%02d', intdiv($workMinutes, 60), $workMinutes % 60) : '';

            $attendanceList[] = [
                'date' => $displayDate,
                'clockIn' => $clockIn,
                'clockOut' => $clockOut,
                'breakTime' => $breakTime,
                'workTime' => $workTime,
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
            'date' => $targetDate,
        ]);

        $isPending = $targetRecord->attendanceCorrectRequest()
            ->where('status', CorrectRequestStatus::Pending->value)
            ->exists();

        return view('attendance.detail', compact('targetDate', 'targetRecord', 'displayDate','isPending'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance)
    {
        //
    }
}

