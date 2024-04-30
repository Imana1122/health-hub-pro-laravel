<?php

namespace Tests\Unit;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class SendSMSTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_example(): void
    {
        $client = new Client();
        $response = $client->post('https://sms.aakashsms.com/sms/v3/send', [
            'form_params' => [
                'auth_token' => 'c1eecbd817abc78626ee119a530b838ef57f8dad9872d092ab128776a00ed31d',
                'to' => 9815335034,
                'text' => "You can change your password here",
            ],
        ]);



        if ($response->getStatusCode() === 200) {
            $this->assertTrue(true);
        }else{
            $this->assertTrue(false);

        }
    }
}
