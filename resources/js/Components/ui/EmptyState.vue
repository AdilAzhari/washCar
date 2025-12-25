<script setup lang="ts">
import { Button } from '@/Components/ui'
import type { Component } from 'vue'

defineProps<{
  icon?: Component
  title: string
  message: string
  actionLabel?: string
  helpText?: string
}>()

const emit = defineEmits<{
  action: []
}>()
</script>

<template>
  <div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <!-- Icon/Illustration -->
    <div v-if="icon" class="mb-4">
      <div class="w-16 h-16 rounded-full bg-muted/50 flex items-center justify-center">
        <component :is="icon" class="h-8 w-8 text-muted-foreground" />
      </div>
    </div>

    <!-- Title -->
    <h3 class="text-lg font-semibold text-foreground mb-2">
      {{ title }}
    </h3>

    <!-- Message -->
    <p class="text-sm text-muted-foreground max-w-sm mb-6">
      {{ message }}
    </p>

    <!-- Action Button -->
    <Button
      v-if="actionLabel"
      variant="primary-action"
      @click="emit('action')"
    >
      {{ actionLabel }}
    </Button>

    <!-- Help Text -->
    <p v-if="helpText" class="text-xs text-muted-foreground mt-4 max-w-md">
      {{ helpText }}
    </p>
  </div>
</template>
