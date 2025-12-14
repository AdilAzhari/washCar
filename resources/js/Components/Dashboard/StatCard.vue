<script setup lang="ts">
import { Card, CardContent } from '@/Components/ui'
import type { Component } from 'vue'

defineProps<{
  title: string
  value: string | number
  subtitle?: string
  icon: Component
  trend?: {
    value: string
    positive: boolean
  }
  iconClassName?: string
}>()
</script>

<template>
  <Card class="stat-card hover-lift border-border/50">
    <CardContent class="p-6">
      <div class="flex items-start justify-between">
        <div class="flex-1">
          <p class="text-sm font-medium text-muted-foreground mb-1">{{ title }}</p>
          <h3 class="text-3xl font-bold text-foreground mb-1">{{ value }}</h3>
          <p v-if="subtitle" class="text-xs text-muted-foreground">{{ subtitle }}</p>
          <div
            v-if="trend"
            :class="[
              'inline-flex items-center gap-1 mt-2 text-xs font-medium px-2 py-1 rounded-full',
              trend.positive ? 'bg-success/10 text-success' : 'bg-destructive/10 text-destructive'
            ]"
          >
            {{ trend.positive ? '↑' : '↓' }} {{ trend.value }}
          </div>
        </div>
        <div :class="['w-12 h-12 rounded-xl flex items-center justify-center', iconClassName || 'bg-primary/10']">
          <component :is="icon" :class="['h-6 w-6', iconClassName ? '' : 'text-primary']" />
        </div>
      </div>
    </CardContent>
  </Card>
</template>
