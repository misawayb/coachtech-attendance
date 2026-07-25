<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test9_1_自分が行った勤怠情報が全て表示されている(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-07-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-07-02',
            'clock_in' => '10:00:00',
            'clock_out' => '19:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('09:00');
        $response->assertSee('10:00');
    }

    public function test9_2_勤怠一覧画面に遷移した際に現在の月が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('2026/07');
    }

    public function test9_3_前月を押下した時に表示月の前月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-06-10',
            'clock_in' => '09:30:00',
            'clock_out' => '18:30:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?month=2026-06');

        $response->assertSee('2026/06');
        $response->assertSee('09:30');
    }

    public function test9_4_翌月を押下した時に表示月の前月の情報が表示される(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-08-10',
            'clock_in' => '08:30:00',
            'clock_out' => '17:30:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?month=2026-08');

        $response->assertSee('2026/08');
        $response->assertSee('08:30');
    }

    public function test9_5_詳細を押下するとその日の勤怠詳細画面に遷移する(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 15));
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-07-10',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/2026-07-10');

        $response->assertOk();
        $response->assertSee('2026年');
        $response->assertSee('7月10日');
    }
}
