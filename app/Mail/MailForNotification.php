<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MailForNotification extends Mailable
{
    use Queueable, SerializesModels;


    public $messages;

    /**
     * Create a new message instance.
     *
     * @param  string  $messages
     * @return void
     */
    public function __construct( $messages)
    {

        $this->messages = $messages;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('notification.notification_template')
            ->with([

                'messages' => $this->messages,
            ]);
    }
}
