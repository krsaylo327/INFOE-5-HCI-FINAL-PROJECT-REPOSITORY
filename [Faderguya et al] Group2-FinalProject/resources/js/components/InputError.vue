<script setup lang="ts">
defineProps<{
    id?: string;
    message?: string;
}>();
</script>

<template>
    <p
        v-if="message"
        :id="id"
        role="status"
        aria-live="polite"
        class="mt-1 inline-flex w-full items-start gap-2 rounded-md bg-destructive/10 px-3 py-2 text-sm text-destructive ring-1 ring-inset ring-destructive/15 dark:bg-destructive/15 dark:text-destructive-foreground/90"
    >
        <span
            aria-hidden="true"
            class="mt-1 inline-flex size-2.5 rounded-full bg-destructive"
        />
        <span class="leading-snug">{{ message }}</span>
    </p>
</template>
