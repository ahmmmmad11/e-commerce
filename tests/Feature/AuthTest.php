<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login (): void
    {
        $user = User::factory()->create();

        $response = $this->postJson(route('login'), [
            'username' => $user->email,
            'password' => 'password'
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'message', 'user'
        ]);
    }

    public function test_login_with_un_existed_email (): void
    {
        $response = $this->postJson(route('login'), [
            'username' => 'un_found@funded.com',
            'password' => 'password'
        ]);

        $response->assertStatus(404);
        $response->assertJsonStructure([
            'message'
        ]);
    }
}
