<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_勤怠詳細画面に表示されるデータが選択したものになっている()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '田中太郎']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);

        $response->assertSee('田中太郎');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_出勤時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/attendance/' . $attendance->id, [
            'check_in' => '19:00',
            'check_out' => '09:00',
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['check_out' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_休憩開始時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/attendance/' . $attendance->id, [
            'check_in' => '09:00',
            'check_out' => '18:00',
            'break_in' => ['19:00'],
            'break_out' => ['20:00'],
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['break_in.0' => '休憩時間が不適切な値です']);
    }

    public function test_休憩終了時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/attendance/' . $attendance->id, [
            'check_in' => '09:00',
            'check_out' => '18:00',
            'break_in' => ['12:00'],
            'break_out' => ['19:00'],
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['break_out.0' => '休憩時間もしくは退勤時間が不適切な値です']);
    }

    public function test_備考欄が未入力の場合エラーメッセージが表示される()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($admin, 'admin')->post('/admin/attendance/' . $attendance->id, [
            'check_in' => '09:00',
            'check_out' => '18:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors(['note' => '備考を記入してください']);
    }
}