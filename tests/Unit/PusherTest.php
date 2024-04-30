<?php

namespace Tests\Unit;

use App\Events\MessageSent;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class PusherTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_pusher(): void
    {
        // Mock the Event facade to prevent actual broadcasting
        Event::fake();

        // Create a chat message
        $chatMessage = ChatMessage::latest()->first();

        // Dispatch the event
        event(new MessageSent($chatMessage));

        // Assert that the event was dispatched with the correct data
        Event::assertDispatched(MessageSent::class, function ($event) use ($chatMessage) {
            return $event->chatMessage->id === $chatMessage->id;
        });
    }
}
