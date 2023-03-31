<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDocumentTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        File::create(['name' => 'documents/my-document.jpg']);
        File::create(['name' => 'documents/my-document2.jpg']);
    }

    public function test_store_user_documents()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('document.store'), [
                'documents' => [
                    [
                        'type' => 'ssn',
                        'document' => 'documents/my-document.jpg'
                    ]
                ]
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['data', 'message']);
    }

    public function test_add_more_user_documents()
    {
        $this->user->user->update(['documents' => [
            [
                'type' => 'ssn',
                'document' => 'documents/my-document.jpg'
            ]
        ]]);

        $response = $this->actingAs($this->user)
            ->postJson(route('document.store'), [
                'documents' => [
                    [
                        'type' => 'ssn2',
                        'document' => 'documents/my-document2.jpg'
                    ]
                ]
            ]);

        $response->assertStatus(200);

        $response->assertJsonStructure(['data', 'message']);

        $this->assertTrue(count($response->json()['data']['documents']) == 2);
    }
}
