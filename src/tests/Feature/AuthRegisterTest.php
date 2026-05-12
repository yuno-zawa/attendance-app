<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_名前が未入力の場合バリデーションメッセージが表示される()
    {
        $data = [
            'name' => '',
            'email' => 'test@testmail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test_メールアドレスが未入力の場合バリデーションメッセージが表示される()
    {
        $data = [
            'name' => '花沢花子',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_パスワードが8文字以下の場合バリデーションメッセージが表示される()
    {
        $data = [
            'name' => '花沢花子',
            'email' => 'test@testmail.com',
            'password' => 'passwor',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    public function test_パスワードが一致しない場合バリデーションメッセージが表示される()
    {
        $data = [
            'name' => '花沢花子',
            'email' => 'test@testmail.com',
            'password' => 'password',
            'password_confirmation' => 'passwoord',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    public function test_パスワードが未入力の場合バリデーションメッセージが表示される()
    {
        $data = [
            'name' => '花沢花子',
            'email' => 'test@testmail.com',
            'password' => '',
            'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_フォームに内容が入力された場合データが正常に保存される()
    {
        $data = [
        'name' => '花沢花子',
        'email' => 'test@testmail.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        ];

        $response = $this->post('/register', $data);

        $this->assertDatabaseHas('users', [
            'name' => '花沢花子',
            'email' => 'test@testmail.com',
        ]);
    }
}

