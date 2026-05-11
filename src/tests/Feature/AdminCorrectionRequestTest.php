<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceCorrectRequest;
use Carbon\Carbon;

class AdminCorrectionRequestTest extends TestCase
{
    use RefreshDatabase;

    public function test_承認待ちの修正申請が全て表示されている()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '田中太郎']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'note' => '承認待ちテスト',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/stamp_correction_request/list');

        $response->assertSee('田中太郎');
        $response->assertSee('承認待ちテスト');
    }

    public function test_承認済みの修正申請が全て表示されている()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '鈴木花子']);
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'check_in' => Carbon::create(2026, 5, 1, 9, 0, 0),
            'check_out' => Carbon::create(2026, 5, 1, 18, 0, 0),
        ]);

        $this->actingAs($user)->post('/stamp_correction_request/' . $attendance->id, [
            'check_in' => '10:00',
            'check_out' => '19:00',
            'note' => '承認済みテスト',
        ]);

        $correctRequest = AttendanceCorrectRequest::first();
        $this->actingAs($admin, 'admin')->post('/admin/stamp_correction_request/approve/' . $correctRequest->id);

        $response = $this->actingAs($admin, 'admin')->get('/stamp_correction_request/list');

        $response->assertSee('鈴木花子');
        $response->assertSee('承認済みテスト');
    }

    public function test_修正申請の詳細内容が正しく表示されている()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create(['name' => '田中太郎']);
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

        $correctRequest = AttendanceCorrectRequest::first();
        $response = $this->actingAs($admin, 'admin')->get('/admin/stamp_correction_request/approve/' . $correctRequest->id);

        $response->assertSee('田中太郎');
        $response->assertSee('10:00');
        $response->assertSee('19:00');
        $response->assertSee('詳細テスト');
    }

    public function test_修正申請の承認処理が正しく行われる()
    {
        $admin = Admin::factory()->create();
        $user = User::factory()->create();
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

        $this->assertDatabaseHas('attendance_correct_requests', [
            'id' => $correctRequest->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'check_in' => '2026-05-01 10:00:00',
            'check_out' => '2026-05-01 19:00:00',
        ]);
    }
}
