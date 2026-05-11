<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use Carbon\Carbon;

class AttendanceCorrectionTest extends TestCase
{
    use RefreshDatabase;

    public function test_出勤時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '19:00',
            'check_out' => '09:00',
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['check_out' => '出勤時間もしくは退勤時間が不適切な値です']);
    }

    public function test_休憩開始時間が退勤時間より後の場合エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
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
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '09:00',
            'check_out' => '18:00',
            'break_in' => ['12:00'],
            'break_out' => ['19:00'],
            'note' => 'テスト',
        ]);

        $response->assertSessionHasErrors(['break_out.0' => '休憩時間が不適切な値です']);
    }

    public function test_備考欄が未入力の場合エラーメッセージが表示される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $response = $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '09:00',
            'check_out' => '18:00',
            'note' => '',
        ]);

        $response->assertSessionHasErrors(['note' => '備考を記入してください']);
    }

    public function test_修正申請処理が実行される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'note' => '修正テスト',
        ]);

        $this->assertDatabaseHas('attendance_correct_requests', [
            'attendance_id' => $attendance->id,
            'status' => 'pending',
        ]);
    }

    public function test_承認待ちにログインユーザーの申請が全て表示される()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'note' => '修正テスト',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertSee('修正テスト');
    }

    public function test_承認済みに管理者が承認した修正申請が全て表示される()
    {
        $user = User::factory()->create();
        $admin = Admin::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'note' => '承認テスト',
        ]);

        $correctRequest = AttendanceCorrectRequest::first();
        $this->actingAs($admin, 'admin')->post('/admin/stamp_correction_request/approve/' . $correctRequest->id);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');

        $response->assertSee('承認テスト');
    }

    public function test_各申請の詳細を押下すると勤怠詳細画面に遷移する()
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'note' => '詳細テスト',
        ]);

        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);

        $response->assertStatus(200);
        $response->assertSee('詳細テスト');
    }
}