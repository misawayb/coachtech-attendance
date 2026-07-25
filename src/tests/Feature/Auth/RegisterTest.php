<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    private function validPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $overrides);
    }

    public function test1_1_名前が未入力の場合バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', $this->validPayload(['name' => '']));

        $response->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    public function test1_2_メールアドレスが未入力の場合バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', $this->validPayload(['email' => '']));

        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test1_3_パスワードが8文字未満の場合バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', $this->validPayload([
            'password' => 'short1',
            'password_confirmation' => 'short1',
        ]));

        $response->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    public function test1_4_パスワードが一致しない場合バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', $this->validPayload([
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ]));

        $response->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    public function test1_5_パスワードが未入力の場合バリデーションメッセージが表示される(): void
    {
        $response = $this->post('/register', $this->validPayload([
            'password' => '',
            'password_confirmation' => '',
        ]));

        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test1_6_フォームに内容が入力されていた場合データが正常に保存される(): void
    {
        $response = $this->post('/register', $this->validPayload());

        $response->assertRedirect('/email/verify');
        $this->assertDatabaseHas('users', [
            'name' => 'テスト太郎',
            'email' => 'test@example.com',
        ]);
    }
}
