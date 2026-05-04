<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericEmail extends Mailable
{
    use SerializesModels;

    public string $content;
    public string $subjectText;

    public function __construct(string $subjectText, string $content)
    {
        $this->subjectText = $subjectText;
        $this->content = $content;
    }

    public function build()
    {
        return $this->subject($this->subjectText)
            ->view('emails.generic')
            ->with([
                'content' => $this->content
            ]);
    }
}
