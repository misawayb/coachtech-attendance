<?php

namespace Database\Factories;

use App\Models\AttendanceCorrectBreak;
use App\Models\AttendanceCorrectRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceCorrectBreak>
 */
class AttendanceCorrectBreakFactory extends Factory
{
    protected $model = AttendanceCorrectBreak::class;

    public function definition(): array
    {
        return [
            'attendance_correct_request_id' => AttendanceCorrectRequest::factory(),
            'break_in' => '12:00:00',
            'break_out' => '13:00:00',
        ];
    }
}
