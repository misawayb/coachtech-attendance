<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\AttendanceRecord;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

        return view('/attendance', compact('user', 'today', 'time', 'status', 'todayRecord'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        //
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
