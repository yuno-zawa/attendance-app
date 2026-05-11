<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\User;

class AttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_自分が行った勤怠情報がすべて表示されている()
    {
        $user = User::factory()->create();
        $now = Carbon::now();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($now->year, $now->month, 1, 9, 0, 0),
            'check_out' => Carbon::create($now->year, $now->month, 1, 18, 0, 0),
        ]);
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($now->year, $now->month, 2, 10, 0, 0),
            'check_out' => Carbon::create($now->year, $now->month, 2, 19, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');

        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_勤怠一覧画面に遷移した際に現在の月が表示される()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance/list');

        $now = Carbon::now();
       $response->assertSee($now->format('Y/m'));
    }

    public function test_前月を押下した時に前月の情報が表示される()
    {
        $user = User::factory()->create();
        $lastMonth = Carbon::now()->subMonth();
        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($lastMonth->year, $lastMonth->month, 1, 9, 0, 0),
            'check_out' => Carbon::create($lastMonth->year, $lastMonth->month, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?month=' . $lastMonth->format('Y-m'));

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_翌月を押下した時に翌月の情報が表示される()
    {
        $user = User::factory()->create();
        $nextMonth = Carbon::now()->addMonth();
        Attendance::factory()->create([
            'user_id' => $user->id,
             'check_in' => Carbon::create($nextMonth->year, $nextMonth->month, 1, 10, 0, 0),
            'check_out' => Carbon::create($nextMonth->year, $nextMonth->month, 1, 19, 0, 0),
        ]);

        $response = $this->actingAs($user)->get('/attendance/list?month=' . $nextMonth->format('Y-m'));

        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_詳細を押下するとその日の勤怠詳細画面に遷移する()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::now()->setHour(9),
            'check_out' => Carbon::now()->setHour(18),
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }
}
