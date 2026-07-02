<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\AttendanceRecord;
use App\Models\AttendanceBreak;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        $earlyTimes = ['15:00:00', '16:00:00', '17:00:00'];
        $overTimes = ['19:00:00', '20:00:00', '21:00:00'];

        foreach ($users as $user) {
            for ($i = 5; $i >= 1; $i--) {
                $month = Carbon::now()->subMonths($i)->startOfMonth();
                $weekdays = $this->getWeekdaysInMonth($month);

                if ($user->id === 1) {
                    $selected = array_slice($weekdays, 0, 15);

                    foreach ($selected as $day) {
                        $this->createAttendance($user->id, $day, '09:00:00', '18:00:00');
                    }
                } else {
                    $workDays = rand(15, 17);
                    $selected = array_slice($weekdays, 0, $workDays);

                    $earlyCount = rand(1, 3);
                    $overtimeCount = rand(1, 3);

                    foreach ($selected as $index => $day) {
                        if ($index < $earlyCount) {
                            $clockOut = $earlyTimes[array_rand($earlyTimes)];
                        } elseif ($index < $earlyCount + $overtimeCount) {
                            $clockOut = $overTimes[array_rand($overTimes)];
                        } else {
                            $clockOut = '18:00:00';
                        }

                        $this->createAttendance($user->id, $day, '09:00:00', $clockOut);
                    }
                }
            }

            $month = Carbon::now()->startOfMonth();
            $weekdays = $this->getWeekdaysInMonth($month);

            if($user->id === 1){
                $selected = array_slice($weekdays, 0, 17);

                foreach($selected as $index => $day ) {
                    if($index < 10) {
                        $clockIn = '09:00:00';
                        $clockOut = '18:00:00';
                    } elseif($index < 13) {
                        $clockIn = '09:00:00';
                        $clockOut = '20:00:00';
                    } elseif($index < 15) {
                        $clockIn = '09:30:00';
                        $clockOut = '18:00:00';
                    } elseif($index < 16) {
                        $clockIn = '09:00:00';
                        $clockOut = '17:00:00';
                    } else {
                        $clockIn = '08:00:00';
                        $clockOut = '21:00:00';
                    }
                    $this->createAttendance($user->id, $day, $clockIn, $clockOut);
                }
            } else {
                $workDays = rand(15, 17);
                $selected = array_slice($weekdays, 0, $workDays);

                $earlyCount = rand(1, 3);
                $overtimeCount = rand(1, 3);

                foreach ($selected as $index => $day) {
                    if ($index < $earlyCount) {
                        $clockOut = $earlyTimes[array_rand($earlyTimes)];
                    } elseif ($index < $earlyCount + $overtimeCount) {
                        $clockOut = $overTimes[array_rand($overTimes)];
                    } else {
                        $clockOut = '18:00:00';
                    }

                    $this->createAttendance($user->id, $day, '09:00:00', $clockOut);
                }
            }
        }
    }

    private function getWeekdaysInMonth(Carbon $month): array
    {
        $weekdays = [];
        $current = $month->copy();
        while ($current->month === $month->month) {
            if ($current->isWeekday()) {
                $weekdays[] = $current->copy();
            }
            $current->addDay();
        }
        return $weekdays;
    }

    private function createAttendance(int $userId, Carbon $day, string $clockIn, string $clockOut): void
    {
        $record = AttendanceRecord::create([
            'user_id' => $userId,
            'date' => $day->toDateString(),
            'clock_in' => $clockIn,
            'clock_out' => $clockOut,
        ]);

        AttendanceBreak::create([
            'attendance_record_id' => $record->id,
            'break_in' => '12:00:00',
            'break_out' => '13:00:00',
        ]);
    }
}
