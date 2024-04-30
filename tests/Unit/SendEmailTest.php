<?php

namespace Tests\Unit;

use App\Mail\MailForNotification;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SendEmailTest extends TestCase
{
     /**
     * A basic email unit test.
     */
    public function test_email_content(): void
    {
        Mail::fake();
        Mail::assertNothingSent();
        Mail::to('imanalimbu@gmail.com')->send(new PasswordResetMail([
            "Hello IMANA!"
        ]));

        Mail::assertSent(PasswordResetMail::class);

    }
}
