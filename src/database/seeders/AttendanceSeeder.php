<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\User;
use Carbon\Carbon;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach($users as $user){
            for ($i = 30; $i >= 1; $i --){
                $date = Carbon::now()->subDays($i);

                $hour = rand(7,10);
                $checkIn = $date->copy()->setTime($hour, 0, 0);
                $checkOut = $date->copy()->setTime($hour + 8 , 0, 0);

                $attendance = Attendance::create([
                    'user_id' => $user->id,
                    'check_in' => $checkIn,
                    'check_out' => $checkOut,
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_in' => $date->copy()->setTime(12, 0, 0),
                    'break_out' => $date->copy()->setTime(13, 0, 0),
                ]);
            }
        }
    }
}
