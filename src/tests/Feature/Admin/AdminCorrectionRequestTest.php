<?php

namespace Tests\Feature\Admin;

use App\Enums\CorrectRequestStatus;
use App\Models\AttendanceCorrectBreak;
use App\Models\AttendanceCorrectRequest;
use App\Models\AttendanceRecord;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test15_1_承認待ちの修正申請が全て表示されている(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $record = AttendanceRecord::factory()->create(['user_id' => $staff->id]);
        AttendanceCorrectRequest::factory()->create([
            'attendance_record_id' => $record->id,
            'comment' => '承認待ちテスト',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list?status=pending');

        $response->assertSee('承認待ちテスト');
    }

    public function test15_2_承認済みの修正申請が全て表示されている(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $record = AttendanceRecord::factory()->create(['user_id' => $staff->id]);
        AttendanceCorrectRequest::factory()->approved()->create([
            'attendance_record_id' => $record->id,
            'comment' => '承認済みテスト',
        ]);

        $response = $this->actingAs($admin)->get('/admin/stamp_correction_request/list?status=approved');

        $response->assertSee('承認済みテスト');
    }

    public function test15_3_修正申請の詳細内容が正しく表示されている(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create(['name' => 'スタッフ次郎']);
        $record = AttendanceRecord::factory()->create(['user_id' => $staff->id]);
        $correctRequest = AttendanceCorrectRequest::factory()->create([
            'attendance_record_id' => $record->id,
            'clock_in' => '09:30:00',
            'clock_out' => '18:30:00',
            'comment' => '詳細確認テスト',
        ]);

        $response = $this->actingAs($admin)->get("/stamp_correction_request/approve/{$correctRequest->id}");

        $response->assertSee('スタッフ次郎');
        $response->assertSee('詳細確認テスト');
    }

    public function test15_4_修正申請の承認処理が正しく行われる(): void
    {
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
        $correctRequest = AttendanceCorrectRequest::factory()->create([
            'attendance_record_id' => $record->id,
            'clock_in' => '09:30:00',
            'clock_out' => '18:30:00',
            'comment' => '修正申請',
        ]);
        AttendanceCorrectBreak::factory()->create([
            'attendance_correct_request_id' => $correctRequest->id,
            'break_in' => '12:00:00',
            'break_out' => '13:00:00',
        ]);

        $response = $this->actingAs($admin)->post("/stamp_correction_request/approve/{$correctRequest->id}");

        $response->assertRedirect(route('correction.show', ['attendance_correct_request_id' => $correctRequest->id]));
        $this->assertDatabaseHas('attendance_records', [
            'id' => $record->id,
            'clock_in' => '09:30:00',
            'clock_out' => '18:30:00',
        ]);
        $this->assertDatabaseHas('attendance_correct_requests', [
            'id' => $correctRequest->id,
            'status' => CorrectRequestStatus::Approved->value,
        ]);
    }
}
