<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailForNotification extends Mailable
{
    use Queueable, SerializesModels;


    public $messages;
    public $title;

    /**
     * Create a new message instance.
     *

     * @param  string  $messages
     * @return void
     */
    public function __construct( $messages,$title)
    {

        $this->messages = $messages;
        $this->title=$title;
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
            ->subject('Dietician Subscription')
            ->view('mails.notification_mail_view')
            ->with([
                'title'=>$this->title,

                'messages' => $this->messages,
            ]);
    }
}
