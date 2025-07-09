<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class EmailControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_email_successfully()
{
    Mail::fake();

    $response = $this->postJson('/api/send-email', [
        'to' => 'test@example.com',
        'subject' => 'Test Subject',
        'body' => 'Test body content',
    ]);

    $response->assertStatus(200)
             ->assertJson(['message' => 'Email sent successfully']);

    Mail::assertSent(SendEmail::class, function ($mail) {
        return $mail->hasTo('test@example.com') &&
               $mail->subjectText === 'Test Subject';
    });
}


    /** @test */
    public function it_requires_email_fields()
    {
        $response = $this->postJson('/api/send-email', []);

        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors(['to', 'subject', 'body']);
    }
}
