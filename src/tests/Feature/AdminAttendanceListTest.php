<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;

    public function test_その日の全ユーザーの勤怠情報が正確に確認できる()
    {
        $admin = Admin::factory()->create();
        $user1 = User::factory()->create(['name' => '田中太郎']);
        $user2 = User::factory()->create(['name' => '鈴木花子']);

        $today = Carbon::today();
        Attendance::factory()->create([
            'user_id' => $user1->id,
            'check_in' => Carbon::create($today->year, $today->month, $today->day, 9, 0, 0),
            'check_out' => Carbon::create($today->year, $today->month, $today->day, 18, 0, 0),
        ]);
        Attendance::factory()->create([
            'user_id' => $user2->id,
            'check_in' => Carbon::create($today->year, $today->month, $today->day, 10, 0, 0),
            'check_out' => Carbon::create($today->year, $today->month, $today->day, 19, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');

        $response->assertSee('田中太郎');
        $response->assertSee('鈴木花子');
        $response->assertSee('09:00');
        $response->assertSee('10:00');
    }

    public function test_遷移した際に現在の日付が表示される()
    {
        $admin = Admin::factory()->create();

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list');

        $today = Carbon::today();
        $response->assertSee($today->format('Y/m/d'));
    }

    public function test_前日を押下した時に前日の勤怠情報が表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '田中太郎']);
        $yesterday = Carbon::yesterday();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($yesterday->year, $yesterday->month, $yesterday->day, 9, 0, 0),
            'check_out' => Carbon::create($yesterday->year, $yesterday->month, $yesterday->day, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list?date=' . $yesterday->format('Y-m-d'));

        $response->assertSee('田中太郎');
        $response->assertSee('09:00');
    }

    public function test_翌日を押下した時に翌日の勤怠情報が表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '鈴木花子']);
        $tomorrow = Carbon::tomorrow();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($tomorrow->year, $tomorrow->month, $tomorrow->day, 10, 0, 0),
            'check_out' => Carbon::create($tomorrow->year, $tomorrow->month, $tomorrow->day, 19, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/list?date=' . $tomorrow->format('Y-m-d'));

        $response->assertSee('鈴木花子');
        $response->assertSee('10:00');
    }
}