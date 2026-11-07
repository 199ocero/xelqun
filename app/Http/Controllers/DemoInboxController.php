<?php

namespace App\Http\Controllers;

use App\Listeners\CaptureDemoMail;
use App\Models\DemoMail;
use Inertia\Inertia;
use Inertia\Response;

class DemoInboxController extends Controller
{
    /**
     * Only the mail this visitor caused. Without a key there's nothing to show.
     */
    public function __invoke(): Response
    {
        return Inertia::render('demo/Inbox', [
            'mails' => DemoMail::query()
                ->where('inbox', session(CaptureDemoMail::INBOX_KEY, ''))
                ->latest('id')
                ->limit(20)
                ->get(['id', 'recipient', 'subject', 'html', 'created_at'])
                ->map(fn (DemoMail $mail): array => [
                    'id' => $mail->id,
                    'recipient' => $mail->recipient,
                    'subject' => $mail->subject,
                    'html' => $mail->html,
                    'sentAt' => $mail->created_at?->diffForHumans(),
                ]),
            'loginUrl' => route('xelqun-panels.admin.auth.login'),
        ]);
    }
}
