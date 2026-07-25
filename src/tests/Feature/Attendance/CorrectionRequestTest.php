<?php

namespace Tests\Feature\Attendance;

use App\Enums\CorrectRequestStatus;
use App\Models\AttendanceCorrectRequest;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    private function createRecord(User $user, string $date = '2026-07-10'): AttendanceRecord
    {
        return AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
    }

    public function test11_1_出勤時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $this->createRecord($user);

        $response = $this->actingAs($user)->post('/attendance/detail/2026-07-10', [
            'clock_in' => '19:00',
            'clock_out' => '18:00',
            'comment' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['clock_in' => '出勤時間が不適切な値です']);
    }

    public function test11_2_休憩開始時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $this->createRecord($user);

        $response = $this->actingAs($user)->post('/attendance/detail/2026-07-10', [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['break_in' => '19:00', 'break_out' => ''],
            ],
            'comment' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_in' => '休憩時間が不適切な値です']);
    }

    public function test11_3_休憩終了時間が退勤時間より後になっている場合エラーメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $this->createRecord($user);

        $response = $this->actingAs($user)->post('/attendance/detail/2026-07-10', [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'breaks' => [
                ['break_in' => '12:00', 'break_out' => '19:00'],
            ],
            'comment' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['breaks.0.break_out' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test11_4_備考欄が未入力の場合のエラーメッセージが表示される(): void
    {
        $user = User::factory()->create();
        $this->createRecord($user);

        $response = $this->actingAs($user)->post('/attendance/detail/2026-07-10', [
            'clock_in' => '09:00',
            'clock_out' => '18:00',
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment' => '備考を記入してください']);
    }

    public function test11_5_修正申請処理が実行される(): void
    {
        $user = User::factory()->create();
        $record = $this->createRecord($user);

        $response = $this->actingAs($user)->post('/attendance/detail/2026-07-10', [
            'clock_in' => '09:30',
            'clock_out' => '18:30',
            'comment' => '電車遅延のため',
        ]);

        $response->assertRedirect(route('attendance.edit', ['date' => '2026-07-10']));
        $this->assertDatabaseHas('attendance_correct_requests', [
            'attendance_record_id' => $record->id,
            'comment' => '電車遅延のため',
            'status' => CorrectRequestStatus::Pending->value,
        ]);
    }

    public function test11_6_承認待ちにログインユーザーが行った申請が全て表示されていること(): void
    {
        $user = User::factory()->create();
        $record = $this->createRecord($user);
        AttendanceCorrectRequest::factory()->create([
            'attendance_record_id' => $record->id,
            'comment' => '承認待ちの申請',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?status=pending');

        $response->assertSee('承認待ちの申請');
    }

    public function test11_7_承認済みに管理者が承認した修正申請が全て表示されている(): void
    {
        $user = User::factory()->create();
        $record = $this->createRecord($user);
        AttendanceCorrectRequest::factory()->approved()->create([
            'attendance_record_id' => $record->id,
            'comment' => '承認済みの申請',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?status=approved');

        $response->assertSee('承認済みの申請');
    }

    public function test11_8_各申請の詳細を押下すると勤怠詳細画面に遷移する(): void
    {
        $user = User::factory()->create();
        $record = $this->createRecord($user);
        AttendanceCorrectRequest::factory()->create([
            'attendance_record_id' => $record->id,
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list?status=pending');

        $response->assertSee(route('attendance.edit', ['date' => '2026-07-10']), false);
    }
}
