<script setup lang="ts">
import { Ticket, Wrench } from 'lucide-vue-next';
</script>

<template>
    <div class="relative flex aspect-square size-8 items-center justify-center rounded-md bg-sidebar-primary text-sidebar-primary-foreground">
        <Ticket class="size-5 text-white dark:text-black" />
        <div class="absolute -bottom-0.5 -right-0.5 flex h-3 w-3 items-center justify-center rounded-sm bg-orange-500">
            <Wrench class="h-1.5 w-1.5 text-white" />
        </div>
    </div>
    <div class="ml-1 grid flex-1 text-left text-sm">
        <span class="mb-0.5 truncate leading-tight font-semibold">Tech Ticket</span>
    </div>
</template>
