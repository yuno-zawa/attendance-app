<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤怠詳細画面の名前がログインユーザーの氏名になっている()
    {
        $user = User::factory()->create(['name' => '田中太郎']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertSee('田中太郎');
    }

    public function test_勤怠詳細画面の日付が選択した日付になっている()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertSee('2026年');
        $response->assertSee('5月1日');
    }

    public function test_出勤退勤の時間がログインユーザーの打刻と一致している()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_休憩の時間がログインユーザーの打刻と一致している()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);
        $attendance->breakTimes()->create([
            'break_in' => Carbon::create(2026, 5, 1, 12, 0, 0),
            'break_out' => Carbon::create(2026, 5, 1, 13, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}