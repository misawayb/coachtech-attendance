<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AttendanceRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $targetDate = Carbon::parse($request->query('date', now()->format('Y-m-d')));

        $users = User::with(['attendanceRecord' => fn($q) => $q->whereDate('date', $targetDate)])->get();

        $attendanceList = $users->map(function ($user) {
            $record = $user->attendanceRecord->first();

            return [
                'name' => $user->name,
                'clockIn' => $record && $record->clock_in ? Carbon::parse($record->clock_in)->format('H:i') : '',
                'clockOut' => $record && $record->clock_out ? Carbon::parse($record->clock_out)->format('H:i') : '',
                'breakTime' => $record ? $record->break_time : '',
                'workTime' => $record ? $record->work_time : '',
                'detailUrl' => $record ? route('admin.edit', ['id' => $record->id]) : null,
            ];
        });

        return view('admin.attendance.list', compact('attendanceList', 'targetDate'));
    }

    public function staffIndex()
    {
        $users = User::all();

        return view('admin.staff.list', compact('users'));
    }

    public function staffAttendance(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $targetMonth = Carbon::parse($request->query('month', now()->format('Y-m')));
        $period = CarbonPeriod::create($targetMonth->copy()->startOfMonth(), $targetMonth->copy()->endOfMonth());

        $targetMonthAttendance = AttendanceRecord::where('user_id', $user->id)
            ->whereBetween('date', [$targetMonth->copy()->startOfMonth(), $targetMonth->copy()->endOfMonth()])
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
                'detailUrl' => $record ? route('admin.edit', ['id' => $record->id]) : null,
            ];
        }

        return view('admin.attendance.staff', compact('attendanceList', 'targetMonth', 'user'));
    }

    public function edit($id)
    {
        $targetRecord = AttendanceRecord::with(['attendanceBreak', 'user'])->findOrFail($id);

        return view('admin.attendance.detail', compact('targetRecord'));
    }

    public function update(Request $request, $id)
    {
        $targetRecord = AttendanceRecord::findOrFail($id);

        $targetRecord->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'comment' => $request->comment,
        ]);

        $targetRecord->attendanceBreak()->delete();

        foreach ($request->breaks ?? [] as $break) {
            if (empty($break['break_in']) && empty($break['break_out'])) {
                continue;
            }

            $targetRecord->attendanceBreak()->create([
                'break_in' => $break['break_in'] ?? null,
                'break_out' => $break['break_out'] ?? null,
            ]);
        }

        return redirect()->route('admin.edit', ['id' => $id])->with('message', '勤怠情報を修正しました');
    }
}
