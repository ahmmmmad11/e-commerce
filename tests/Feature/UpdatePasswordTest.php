<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UpdatePasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_update_password()
    {
        $response = $this->actingAs($this->user)
            ->putJson(route('user.update.password'), [
            'old_password' => 'password',
            'new_password' => '12345678',
            'new_password_confirmation' => '12345678',
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['message']);

        $this->assertTrue(Hash::check('12345678', User::find($this->user->id)->password));
    }
}
