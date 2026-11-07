<?php

namespace App\Listeners;

use App\Models\DemoMail;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

/**
 * Nothing leaves the demo. Anyone can type any address into its forms, so every
 * email lands in the inbox of the visitor who caused it.
 */
class CaptureDemoMail
{
    public const string INBOX_KEY = 'demo.inbox';

    public function handle(MessageSending $event): bool
    {
        if (! session()->has(self::INBOX_KEY)) {
            session()->put(self::INBOX_KEY, (string) Str::uuid());
        }

        DemoMail::query()->create([
            'inbox' => session(self::INBOX_KEY),
            'recipient' => collect($event->message->getTo())->map->getAddress()->implode(', '),
            'subject' => (string) $event->message->getSubject(),
            'html' => (string) ($event->message->getHtmlBody() ?? e((string) $event->message->getTextBody())),
        ]);

        return false;
    }
}
