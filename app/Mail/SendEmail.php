<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

// class SendEmail extends Mailable
// {
//     use Queueable, SerializesModels;

//     public $subject;
//     public $body;

//     public function __construct($subject, $body)
//     {
//         $this->subject = $subject;
//         $this->body = $body;
//     }

//     public function build()
//     {
//         return $this->subject($this->subject)
//                     ->view('emails.default')
//                     ->with([
//                         'subject' => $this->subject,
//                         'body' => $this->body,
//                     ]);
//     }
// }

class SendEmail extends Mailable
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


