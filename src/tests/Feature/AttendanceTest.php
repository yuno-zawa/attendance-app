<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{

    use RefreshDatabase;

    public function test_勤務外の場合、勤怠ステータスが正しく表示される()
    {
        $user = User::factory()->create();

        $responce = $this->actingAs($user)->get('/attendance');

        $responce->assertSee('勤務外');
    }

    public function test_出勤中の場合、勤怠ステータスが正しく表示される()
    {
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'check_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_休憩中の場合、勤怠ステータスが正しく表示される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'check_out' => null,
        ]);

        $attendance->breakTimes()->create([
            'break_in' => Carbon::now(),
            'break_out' => null,
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_退勤済の場合、勤怠ステータスが正しく表示される()
    {
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'check_out' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤済');
    }

    public function test_出勤ボタンが正しく機能する()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/check-in');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_出勤は一日一回のみできる()
    {
        $user = User::factory()->create();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::now(),
            'check_out' => Carbon::now(),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertDontSee('<button type="submit" class="attendance-button">出勤</button>', false);
    }

    public function test_出勤時間が勤怠一覧画面で確認できる()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/check-in');

        $response = $this->actingAs($user)->get('/attendance/list');

        $now = Carbon::now();
        $response->assertSee($now->format('H:i'));
    }

    public function test_休憩ボタンが正しく機能する()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/check-in');

        $this->actingAs($user)->post('/attendance/break-in');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩中');
    }

    public function test_休憩は一日に何回でもできる()
    {
         $user = User::factory()->create();

         $this->actingAs($user)->post('/attendance/check-in');

         $this->actingAs($user)->post('/attendance/break-in');
         $this->actingAs($user)->post('/attendance/break-out');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');
    }

    public function test_休憩戻ボタンが正しく機能する()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/check-in');
        $this->actingAs($user)->post('/attendance/break-in');

        $this->actingAs($user)->post('/attendance/break-out');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('出勤中');
    }

    public function test_休憩時刻が勤怠一覧画面で確認できる()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::today()->setHour(9),
            'check_out' => Carbon::today()->setHour(18),
        ]);

        $attendance->breakTimes()->create([
            'break_in' => Carbon::today()->setHour(12)->setMinute(0),
            'break_out' => Carbon::today()->setHour(13)->setMinute(0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('1:00');
    }

    public function test_退勤ボタンが正しく機能する()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/check-in');
        $this->actingAs($user)->post('/attendance/check-out');

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('退勤済');
    }

    public function test_退勤時間が勤怠一覧画面で確認できる()
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/attendance/check-in');
        $this->actingAs($user)->post('/attendance/check-out');

        $response = $this->actingAs($user)->get('/attendance/list');

        $now = Carbon::now();
        $response->assertSee($now->format('H:i'));
    }
}