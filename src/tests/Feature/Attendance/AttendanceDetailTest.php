<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceBreak;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private function createRecordWithBreak(User $user, string $date): AttendanceRecord
    {
        $record = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        AttendanceBreak::factory()->create([
            'attendance_record_id' => $record->id,
            'break_in' => '12:00:00',
            'break_out' => '13:00:00',
        ]);

        return $record;
    }

    public function test10_1_勤怠詳細画面の名前がログインユーザーの氏名になっている(): void
    {
        $user = User::factory()->create(['name' => 'テスト花子']);
        $this->createRecordWithBreak($user, '2026-07-10');

        $response = $this->actingAs($user)->get('/attendance/detail/2026-07-10');

        $response->assertSee('テスト花子');
    }

    public function test10_2_勤怠詳細画面の日付が選択した日付になっている(): void
    {
        $user = User::factory()->create();
        $this->createRecordWithBreak($user, '2026-07-10');

        $response = $this->actingAs($user)->get('/attendance/detail/2026-07-10');

        $response->assertSee('2026年');
        $response->assertSee('7月10日');
    }

    public function test10_3_出勤退勤にて記されている時間がログインユーザーの打刻と一致している(): void
    {
        $user = User::factory()->create();
        $this->createRecordWithBreak($user, '2026-07-10');

        $response = $this->actingAs($user)->get('/attendance/detail/2026-07-10');

        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }

    public function test10_4_休憩にて記されている時間がログインユーザーの打刻と一致している(): void
    {
        $user = User::factory()->create();
        $this->createRecordWithBreak($user, '2026-07-10');

        $response = $this->actingAs($user)->get('/attendance/detail/2026-07-10');

        $response->assertSee('value="12:00"', false);
        $response->assertSee('value="13:00"', false);
    }
}
