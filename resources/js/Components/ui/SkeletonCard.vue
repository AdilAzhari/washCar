<script setup lang="ts">
import { Card, CardContent } from '@/Components/ui'
import Skeleton from './Skeleton.vue'

defineProps<{
  showHeader?: boolean
  lines?: number
}>()
</script>

<template>
  <Card>
    <CardContent class="p-6 space-y-4">
      <!-- Header Skeleton -->
      <div v-if="showHeader" class="flex items-start justify-between">
        <div class="space-y-2 flex-1">
          <Skeleton class="h-6 w-3/4" />
          <Skeleton class="h-4 w-1/2" />
        </div>
        <Skeleton class="h-6 w-16" />
      </div>

      <!-- Content Lines -->
      <div class="space-y-3">
        <Skeleton v-for="i in (lines || 3)" :key="i" class="h-4 w-full" />
      </div>

      <!-- Action Skeleton -->
      <div class="flex gap-2 pt-2">
        <Skeleton class="h-10 flex-1" />
        <Skeleton class="h-10 w-20" />
      </div>
    </CardContent>
  </Card>
</template>
