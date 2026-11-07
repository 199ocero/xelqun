<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{
    mails: {
        id: number;
        recipient: string;
        subject: string;
        html: string;
        sentAt: string;
    }[];
    loginUrl: string;
}>();
</script>

<template>
    <div class="min-h-dvh bg-xq-shell text-xq-foreground">
        <Head title="Demo inbox" />
        <main class="mx-auto grid max-w-3xl gap-4 px-5 py-10" data-xq-page>
            <header>
                <h1 class="text-2xl font-semibold tracking-tight">
                    Demo inbox
                </h1>
                <p class="mt-1 text-sm text-xq-muted-foreground">
                    The demo never sends real email. What it would have sent you
                    lands here, and only you can see it.
                    <a :href="loginUrl" class="text-xq-primary hover:underline"
                        >Back to the demo</a
                    >
                </p>
            </header>
            <p
                v-if="mails.length === 0"
                class="rounded-xq-lg border border-xq-border bg-xq-card p-8 text-center text-sm text-xq-muted-foreground"
            >
                Nothing yet. Try the forgot password link, or register an
                account.
            </p>
            <article
                v-for="mail in mails"
                :key="mail.id"
                class="overflow-hidden rounded-xq-lg border border-xq-border bg-xq-card"
            >
                <header class="border-b border-xq-border px-4 py-3">
                    <h2 class="font-medium">{{ mail.subject }}</h2>
                    <p class="text-xs text-xq-muted-foreground">
                        To {{ mail.recipient }}, {{ mail.sentAt }}
                    </p>
                </header>
                <iframe
                    :srcdoc="mail.html"
                    :title="mail.subject"
                    sandbox="allow-top-navigation-by-user-activation"
                    class="h-96 w-full bg-white"
                ></iframe>
            </article>
        </main>
    </div>
</template>
