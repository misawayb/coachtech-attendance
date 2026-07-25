<?php

namespace Tests\Feature\Admin;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test14_1_管理者ユーザーが全一般ユーザーの氏名メールアドレスを確認できる(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->create(['name' => 'スタッフ一郎', 'email' => 'staff-ichiro@example.com']);

        $response = $this->actingAs($admin)->get('/admin/staff/list');

        $response->assertSee('スタッフ一郎');
        $response->assertSee('staff-ichiro@example.com');
    }

    public function test14_2_ユーザーの勤怠情報が正しく表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-07-05',
            'clock_in' => '09:15:00',
            'clock_out' => '18:15:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$staff->id}");

        $response->assertSee('09:15');
        $response->assertSee('18:15');
    }

    public function test14_3_前月を押下した時に表示月の前月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-06-05',
            'clock_in' => '08:45:00',
            'clock_out' => '17:45:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$staff->id}?month=2026-06");

        $response->assertSee('2026/06');
        $response->assertSee('08:45');
    }

    public function test14_4_翌月を押下した時に表示月の前月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-08-05',
            'clock_in' => '10:00:00',
            'clock_out' => '19:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/staff/{$staff->id}?month=2026-08");

        $response->assertSee('2026/08');
        $response->assertSee('10:00');
    }

    public function test14_5_詳細を押下するとその日の勤怠詳細画面に遷移する(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-07-05',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->get("/admin/attendance/{$record->id}");

        $response->assertOk();
    }
}
