<script setup lang="ts">
import { DialogTrigger, type DialogTriggerProps } from 'radix-vue'
import { cn } from '@/lib/utils'
import { type HTMLAttributes } from 'vue'

const props = defineProps<DialogTriggerProps & { class?: HTMLAttributes['class'] }>()
</script>

<template>
  <DialogTrigger :class="cn(props.class)" v-bind="props">
    <slot />
  </DialogTrigger>
</template>
