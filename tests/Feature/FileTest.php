<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class FileTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    public function test_upload_image()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('files.store'), [
                'file' => UploadedFile::fake()->image('profile.png'),
            ]);

        $response->assertStatus(201);

        $response->assertJsonStructure(['data']);
    }

    public function test_upload_pdf()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('files.store'), [
                'type' => 'document',
                'file' => UploadedFile::fake()->create('doc.pdf', 10),
            ]);

        $response->assertStatus(201);

        $response->assertJsonStructure(['data']);
    }

    public function test_can_not_upload_file_other_than_pdf_png_jpg_svg()
    {
        $response = $this->actingAs($this->user)
            ->postJson(route('files.store'), [
                'type' => 'document',
                'file' => UploadedFile::fake()->create('doc.pptx', 10),
            ]);

        $response->assertStatus(422);

        $response->assertJsonValidationErrorFor('file');
    }
}
