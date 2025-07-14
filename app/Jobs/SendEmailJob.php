<?php

namespace App\Jobs;

use App\Mail\SendEmail;
use App\Models\Email;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;

    /**
     * Create a new job instance.
     *
     * @param \App\Models\Email $email
     */
    public function __construct(Email $email)
    {
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            Mail::to($this->email->to)->send(new SendEmail($this->email->subject, $this->email->body, $this->email->id));

            // Update email status to 'sent' and set sent_at timestamp
            $this->email->status = 'sent';
            $this->email->sent_at = now();
            $this->email->save();

            Log::info("Email ID {$this->email->id} sent successfully.");
        } catch (\Exception $e) {
            // Update status to 'failed' if there was an error
            $this->email->status = 'failed';
            $this->email->save();

            Log::error("Failed to send email ID {$this->email->id}: " . $e->getMessage());
        }
    }
}
