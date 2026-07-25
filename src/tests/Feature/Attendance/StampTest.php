<?php

namespace Tests\Feature\Attendance;

use App\Models\AttendanceBreak;
use App\Models\AttendanceRecord;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StampTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    // 項目4：日時取得機能

    public function test4_1_現在の日時情報がUIと同じ形式で出力されている(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 25, 10, 30, 0));
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee(Carbon::now()->isoFormat('YYYY年M月D日(ddd)'));
        $response->assertSee('10:30');
    }

    // 項目5：ステータス確認機能

    public function test5_1_勤務外の場合勤怠ステータスが正しく表示される(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('勤務外');
    }

    public function test5_2_出勤中の場合勤怠ステータスが正しく表示される(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test5_3_休憩中の場合勤怠ステータスが正しく表示される(): void
    {
        $user = User::factory()->create();
        $record = AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);
        AttendanceBreak::factory()->create([
            'attendance_record_id' => $record->id,
            'break_in' => '12:00:00',
            'break_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test5_4_退勤済の場合勤怠ステータスが正しく表示される(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤済');
    }

    // 項目6：出勤機能

    public function test6_1_出勤ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();

        $before = $this->actingAs($user)->get('/attendance');
        $before->assertSee('出勤');

        $this->actingAs($user)->post('/attendance')->assertRedirect('/attendance');

        $after = $this->actingAs($user)->get('/attendance');
        $after->assertSee('出勤中');
        $this->assertDatabaseHas('attendance_records', [
            'user_id' => $user->id,
            'date' => now()->toDateString(),
        ]);
    }

    public function test6_2_出勤は一日一回のみできる(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('出勤');
    }

    public function test6_3_出勤時刻が勤怠一覧画面で確認できる(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 25, 9, 5, 0));
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance');

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('09:05');
    }

    // 項目7：休憩機能

    public function test7_1_休憩ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        $before = $this->actingAs($user)->get('/attendance');
        $before->assertSee('休憩入');

        $this->actingAs($user)->post('/attendance', ['action' => 'break_in'])->assertRedirect('/attendance');

        $after = $this->actingAs($user)->get('/attendance');
        $after->assertSee('休憩中');
    }

    public function test7_2_休憩は一日に何回でもできる(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);
        $this->actingAs($user)->post('/attendance');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩入');
    }

    public function test7_3_休憩戻ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);

        $this->actingAs($user)->post('/attendance')->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test7_4_休憩戻は一日に何回でもできる(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);
        $this->actingAs($user)->post('/attendance');
        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩戻');
    }

    public function test7_5_休憩時刻が勤怠一覧画面で確認できる(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 25, 12, 0, 0));
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        $this->actingAs($user)->post('/attendance', ['action' => 'break_in']);
        Carbon::setTestNow(Carbon::create(2026, 7, 25, 13, 0, 0));
        $this->actingAs($user)->post('/attendance');

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('1:00');
    }

    // 項目8：退勤機能

    public function test8_1_退勤ボタンが正しく機能する(): void
    {
        $user = User::factory()->create();
        AttendanceRecord::factory()->create([
            'user_id' => $user->id,
            'date' => now()->toDateString(),
            'clock_in' => '09:00:00',
            'clock_out' => null,
        ]);

        $before = $this->actingAs($user)->get('/attendance');
        $before->assertSee('退勤');

        $this->actingAs($user)->post('/attendance', ['action' => 'clock_out'])->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤済');
    }

    public function test8_2_退勤時刻が勤怠一覧画面で確認できる(): void
    {
        Carbon::setTestNow(Carbon::create(2026, 7, 25, 9, 0, 0));
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance');

        Carbon::setTestNow(Carbon::create(2026, 7, 25, 18, 30, 0));
        $this->actingAs($user)->post('/attendance', ['action' => 'clock_out']);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('18:30');
    }
}
