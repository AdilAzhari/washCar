<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue'
import { type CardVariants, cardVariants } from './variants'
import { cn } from '@/lib/utils'

interface Props {
  variant?: CardVariants['variant']
  class?: HTMLAttributes['class']
}

const props = withDefaults(defineProps<Props>(), {
  variant: 'default',
})
</script>

<template>
  <div :class="cn(cardVariants({ variant }), props.class)">
    <slot />
  </div>
</template>
