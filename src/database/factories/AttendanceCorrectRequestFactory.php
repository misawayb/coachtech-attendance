<?php

namespace Database\Factories;

use App\Enums\CorrectRequestStatus;
use App\Models\AttendanceCorrectRequest;
use App\Models\AttendanceRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AttendanceCorrectRequest>
 */
class AttendanceCorrectRequestFactory extends Factory
{
    protected $model = AttendanceCorrectRequest::class;

    public function definition(): array
    {
        return [
            'attendance_record_id' => AttendanceRecord::factory(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'comment' => 'テスト申請',
            'status' => CorrectRequestStatus::Pending->value,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CorrectRequestStatus::Approved->value,
        ]);
    }
}
