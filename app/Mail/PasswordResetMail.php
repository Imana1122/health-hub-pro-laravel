<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetMail extends Mailable
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
        // dd($this->messages);
        return $this->from(ENV('MAIL_FROM_ADDRESS'))
            ->subject('Password Reset')
            ->view('mails.password_reset_mail_view')
            ->with([

                'messages' => $this->messages,
            ]);
    }
}
