<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEmail extends Mailable implements ShouldQueue

{
    use Queueable, SerializesModels;

    public $subjectText;
    public $bodyText;
    public $id;

    public function __construct($subjectText, $bodyText, $id)
    {
        $this->subjectText = $subjectText;
        $this->bodyText = $bodyText;
        $this->id = $id;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
                    ->view('emails.default')
                    ->with([
                        'subject' => $this->subjectText,
                        'body' => $this->bodyText,
                        'id' => $this->id,
                    ]);
    }
}


