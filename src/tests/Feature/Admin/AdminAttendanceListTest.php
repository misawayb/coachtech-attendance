<?php

namespace Tests\Feature\Admin;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test12_1_その日になされた全ユーザーの勤怠情報が正確に確認できる(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 20));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create(['name' => 'スタッフ太郎']);
        AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-07-20',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee('スタッフ太郎');
        $response->assertSee('09:00');
    }

    public function test12_2_遷移した際に現在の日付が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 20));
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin/attendance/list');

        $response->assertSee('2026/07/20');
    }

    public function test12_3_前日を押下した時に前の日の勤怠情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 20));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-07-19',
            'clock_in' => '08:00:00',
            'clock_out' => '17:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=2026-07-19');

        $response->assertSee('2026/07/19');
        $response->assertSee('08:00');
    }

    public function test12_4_翌日を押下した時に次の日の勤怠情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 20));
        $admin = User::factory()->admin()->create();
        $staff = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $staff->id,
            'date' => '2026-07-21',
            'clock_in' => '07:00:00',
            'clock_out' => '16:00:00',
        ]);

        $response = $this->actingAs($admin)->get('/admin/attendance/list?date=2026-07-21');

        $response->assertSee('2026/07/21');
        $response->assertSee('07:00');
    }
}
