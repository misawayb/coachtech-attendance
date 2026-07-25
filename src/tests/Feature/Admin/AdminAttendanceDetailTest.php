<?php

namespace Tests\Feature\Admin;

use App\Models\AttendanceBreak;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    private function createRecord(): AttendanceRecord
    {
        $staff = User::factory()->create(['name' => 'スタッフ花子']);

        $record = AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-07-10',
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

    public function test13_1_勤怠詳細画面に表示されるデータが選択したものになっている(): void
    {
        $admin = User::factory()->admin()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($admin)->get("/admin/attendance/{$record->id}");

        $response->assertSee('スタッフ花子');
        $response->assertSee('value="09:00"', false);
        $response->assertSee('value="18:00"', false);
    }

    public function test13_2_出勤時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($admin)->post("/admin/attendance/{$record->id}", [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'comment' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test13_3_休憩開始時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($admin)->post("/admin/attendance/{$record->id}", [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['break_in' => '19:00', 'break_out' => ''],
            ],
            'comment' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_in' => '休憩時間が不適切な値です']);
    }

    public function test13_4_休憩終了時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($admin)->post("/admin/attendance/{$record->id}", [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['break_in' => '12:00', 'break_out' => '19:00'],
            ],
            'comment' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_out' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test13_5_備考欄が未入力の場合のエラーメッセージが表示される(): void
    {
        $admin = User::factory()->admin()->create();
        $record = $this->createRecord();

        $response = $this->actingAs($admin)->post("/admin/attendance/{$record->id}", [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment' => '備考を記入してください']);
    }
}
