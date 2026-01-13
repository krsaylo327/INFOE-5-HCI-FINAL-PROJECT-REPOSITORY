<script setup lang="ts">
import { home } from '@/routes';
import { Link } from '@inertiajs/vue3';
import { Ticket, Wrench } from 'lucide-vue-next';

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="flex min-h-svh flex-col items-center justify-center gap-6 bg-background p-6 md:p-10"
    >
        <div class="w-full max-w-sm">
            <div class="flex flex-col gap-8">
                <div class="flex flex-col items-center gap-4">
                    <Link
                        :href="home()"
                        class="flex flex-col items-center gap-2 font-medium"
                    >
                        <!-- Tech Ticket Logo -->
                        <div class="relative mb-2">
                            <div class="flex h-16 w-16 items-center justify-center rounded-xl bg-primary shadow-lg">
                                <Ticket class="h-8 w-8 text-primary-foreground" />
                            </div>
                            <div class="absolute -bottom-1 -right-1 flex h-7 w-7 items-center justify-center rounded-md bg-orange-500 shadow-md">
                                <Wrench class="h-4 w-4 text-white" />
                            </div>
                        </div>
                        <span class="text-lg font-bold text-foreground">Tech Ticket</span>
                    </Link>
                    <div class="space-y-2 text-center">
                        <h1 class="text-xl font-medium">{{ title }}</h1>
                        <p class="text-center text-sm text-muted-foreground">
                            {{ description }}
                        </p>
                    </div>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
