<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminStaffListTest extends TestCase
{
    use RefreshDatabase;

    public function test_管理者ユーザーが全一般ユーザーの氏名メールアドレスを確認できる()
    {
        $admin = Admin::factory()->create();
        $user1 = User::factory()->create(['name' => '田中太郎', 'email' => 'tanaka@test.com']);
        $user2 = User::factory()->create(['name' => '鈴木花子', 'email' => 'suzuki@test.com']);

        $response = $this->actingAs($admin, 'admin')->get('/admin/staff/list');

        $response->assertSee('田中太郎');
        $response->assertSee('tanaka@test.com');
        $response->assertSee('鈴木花子');
        $response->assertSee('suzuki@test.com');
    }

    public function test_ユーザーの勤怠情報が正しく表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $now = Carbon::now();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($now->year, $now->month, 1, 9, 0, 0),
            'check_out' => Carbon::create($now->year, $now->month, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/staff/' . $user->id);

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_前月を押下した時に前月の情報が表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $lastMonth = Carbon::now()->subMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($lastMonth->year, $lastMonth->month, 1, 9, 0, 0),
            'check_out' => Carbon::create($lastMonth->year, $lastMonth->month, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/staff/' . $user->id . '?month=' . $lastMonth->format('Y-m'));

        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_翌月を押下した時に翌月の情報が表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $nextMonth = Carbon::now()->addMonth();

        Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create($nextMonth->year, $nextMonth->month, 1, 10, 0, 0),
            'check_out' => Carbon::create($nextMonth->year, $nextMonth->month, 1, 19, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/staff/' . $user->id . '?month=' . $nextMonth->format('Y-m'));

        $response->assertSee('10:00');
        $response->assertSee('19:00');
    }

    public function test_詳細を押下するとその日の勤怠詳細画面に遷移する()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '田中太郎']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee('田中太郎');
    }
}