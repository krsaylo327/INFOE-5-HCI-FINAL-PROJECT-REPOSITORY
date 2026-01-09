<script setup lang="ts">
import type { SelectTriggerProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { ChevronDown } from "lucide-vue-next"
import { SelectIcon, SelectTrigger, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"

const props = withDefaults(
  defineProps<SelectTriggerProps & { class?: HTMLAttributes["class"], size?: "sm" | "default" }>(),
  { size: "default" },
)

const triggerBaseClasses = `
  border-input/90
  data-[placeholder]:text-muted-foreground
  [&_svg:not([class*="text-"])]:text-muted-foreground
  focus-visible:border-ring focus-visible:ring-ring/35
  aria-invalid:ring-destructive/25 dark:aria-invalid:ring-destructive/40
  aria-invalid:border-destructive
  dark:bg-input/30 dark:hover:bg-input/40
  flex w-fit items-center justify-between gap-2
  rounded-lg border bg-background/80 px-3 py-2 text-sm whitespace-nowrap
  shadow-[0_1px_2px_rgba(15,23,42,0.08)] transition-[color,background,box-shadow,border]
  duration-150 ease-out outline-none focus-visible:ring-2
  disabled:cursor-not-allowed disabled:bg-muted/40 disabled:opacity-70
  data-[size=default]:h-10 data-[size=sm]:h-9
  *:data-[slot=select-value]:line-clamp-1
  *:data-[slot=select-value]:flex
  *:data-[slot=select-value]:items-center
  *:data-[slot=select-value]:gap-2
  [&_svg]:pointer-events-none [&_svg]:shrink-0
  [&_svg:not([class*="size-"])]:size-4
`

const delegatedProps = reactiveOmit(props, "class", "size")
const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <SelectTrigger
    data-slot="select-trigger"
    :data-size="size"
    v-bind="forwardedProps"
    :class="cn(
      triggerBaseClasses,
      props.class,
    )"
  >
    <slot />
    <SelectIcon as-child>
      <ChevronDown class="size-4 opacity-50" />
    </SelectIcon>
  </SelectTrigger>
</template>
