<script setup lang="ts">
import type { HTMLAttributes } from "vue"
import { useVModel } from "@vueuse/core"
import { cn } from "@/lib/utils"

const props = defineProps<{
  defaultValue?: string | number
  modelValue?: string | number
  class?: HTMLAttributes["class"]
}>()

const emits = defineEmits<{
  (e: "update:modelValue", payload: string | number): void
}>()

const modelValue = useVModel(props, "modelValue", emits, {
  passive: true,
  defaultValue: props.defaultValue,
})
</script>

<template>
  <input
    v-model="modelValue"
    data-slot="input"
    :class="cn(
      'file:text-foreground placeholder:text-muted-foreground/80 selection:bg-primary/20 selection:text-foreground border-input/90 w-full min-w-0 rounded-lg border bg-background/80 px-3 py-2.5 text-base text-foreground shadow-[0_1px_2px_rgba(15,23,42,0.08)] outline-none transition-[color,background,box-shadow,border] duration-150 ease-out file:inline-flex file:h-8 file:items-center file:rounded-md file:border file:border-input file:bg-secondary file:px-3 file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:bg-muted/40 disabled:text-muted-foreground/70 md:text-sm',
      'focus-visible:border-ring focus-visible:ring-2 focus-visible:ring-ring/35 focus-visible:ring-offset-0',
      'aria-invalid:border-destructive aria-invalid:ring-destructive/25 dark:aria-invalid:ring-destructive/40',
      props.class,
    )"
  >
</template>
