<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;

class MailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_会員登録後認証メールが送信される()
    {
        Notification::fake();

        $this->post('/register', [
            'name' => '田中太郎',
            'email' => 'test@testmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::first();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_認証メール誘導画面で認証はこちらからボタンを押下するとメール認証サイトに遷移する()
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/email/verify');

        $response->assertStatus(200);
    }

    public function test_メール認証を完了すると勤怠登録画面に遷移する()
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        $response->assertRedirect('/attendance?verified=1');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }
}