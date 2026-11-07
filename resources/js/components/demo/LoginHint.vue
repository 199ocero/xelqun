<script setup lang="ts">
defineProps<{ email: string; password: string; inboxUrl: string }>();
</script>

<template>
    <div
        class="grid gap-1 rounded-xq-md border border-xq-border p-3 text-sm"
        data-demo-login-hint
    >
        <p class="font-medium">The demo account is filled in for you.</p>
        <p class="text-xq-muted-foreground">
            Sign in as {{ email }} with {{ password }}. Emails from this demo go
            to your
            <a :href="inboxUrl" class="text-xq-primary hover:underline"
                >demo inbox</a
            >.
        </p>
    </div>
</template>
