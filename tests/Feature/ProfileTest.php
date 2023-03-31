<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        File::create(['name' => 'profile.png']);

        $this->actingAs($this->user);
    }

    public function test_show_profile()
    {
        $response = $this->getJson(route('profile.show', $this->user->id));

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [
            'name',
            'email',
            'phone',
            'user' => [
                'profile_image'
            ]
        ]]);
    }

    public function test_update_profile()
    {
        $response = $this->putJson(route('profile.update', $this->user->id), [
            'name' => 'new name',
            'profile_image' => 'profile.png'
        ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['data' => [
            'name',
            'email',
            'phone',
            'user' => [
                'profile_image'
            ]
        ]]);
    }
}
