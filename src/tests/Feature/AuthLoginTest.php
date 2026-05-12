<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        User::factory()->create([
            'email' => 'test@testmail.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => '',
            'password' => 'password',
        ];

        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        User::factory()->create([
            'email' => 'test@testmail.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'test@testmail.com',
            'password' => '',
        ];

        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_登録内容と一致しない場合バリデーションメッセージが表示される()
    {
        User::factory()->create([
            'email' => 'test@testmail.com',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'testt@testmail.com',
            'password' => 'passwordd',
        ];

        $response = $this->post('/login', $data);

        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }
}