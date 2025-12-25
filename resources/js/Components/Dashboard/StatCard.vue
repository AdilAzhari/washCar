<script setup lang="ts">
import { Card, CardContent } from '@/Components/ui'
import { TrendingUp, TrendingDown } from 'lucide-vue-next'
import type { Component } from 'vue'

const props = defineProps<{
  title: string
  value: string | number
  subtitle?: string
  icon: Component
  trend?: {
    value: string
    positive: boolean
  }
  accentColor?: 'primary' | 'success' | 'warning' | 'bay-active' | 'bay-idle' | 'bay-maintenance' | 'queue-in-progress' | 'tier-regular' | 'tier-gold'
  iconClassName?: string
}>()

const accentColorClasses = {
  primary: 'bg-primary',
  success: 'bg-success',
  warning: 'bg-warning',
  'bay-active': 'bg-bay-active',
  'bay-idle': 'bg-bay-idle',
  'bay-maintenance': 'bg-bay-maintenance',
  'queue-in-progress': 'bg-queue-in-progress',
  'tier-regular': 'bg-tier-regular',
  'tier-gold': 'bg-tier-gold',
}

const iconBgClasses = {
  primary: 'bg-primary/10',
  success: 'bg-success/10',
  warning: 'bg-warning/10',
  'bay-active': 'bg-bay-active/10',
  'bay-idle': 'bg-bay-idle/10',
  'bay-maintenance': 'bg-bay-maintenance/10',
  'queue-in-progress': 'bg-queue-in-progress/10',
  'tier-regular': 'bg-tier-regular/10',
  'tier-gold': 'bg-tier-gold/10',
}

const iconColorClasses = {
  primary: 'text-primary',
  success: 'text-success',
  warning: 'text-warning',
  'bay-active': 'text-bay-active',
  'bay-idle': 'text-bay-idle',
  'bay-maintenance': 'text-bay-maintenance',
  'queue-in-progress': 'text-queue-in-progress',
  'tier-regular': 'text-tier-regular',
  'tier-gold': 'text-tier-gold',
}
</script>

<template>
  <Card class="overflow-hidden hover-lift border-border/50 transition-all duration-200">
    <!-- Colored Accent Bar -->
    <div
      :class="['h-1', accentColor ? accentColorClasses[accentColor] : 'bg-primary']"
    />

    <CardContent class="p-6">
      <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
          <!-- Title -->
          <p class="text-sm font-medium text-muted-foreground mb-2">{{ title }}</p>

          <!-- Value - Large, Bold, Tabular Numbers -->
          <h3 class="text-3xl font-bold text-foreground tabular-nums mb-1">{{ value }}</h3>

          <!-- Subtitle -->
          <p v-if="subtitle" class="text-xs text-muted-foreground">{{ subtitle }}</p>

          <!-- Trend Indicator -->
          <div
            v-if="trend"
            :class="[
              'inline-flex items-center gap-1 mt-3 text-xs font-semibold px-2.5 py-1 rounded-full',
              trend.positive ? 'bg-success/10 text-success' : 'bg-destructive/10 text-destructive'
            ]"
          >
            <TrendingUp v-if="trend.positive" class="h-3 w-3" />
            <TrendingDown v-else class="h-3 w-3" />
            <span class="tabular-nums">{{ trend.value }}</span>
          </div>
        </div>

        <!-- Icon Circle -->
        <div
          :class="[
            'w-14 h-14 rounded-xl flex items-center justify-center flex-shrink-0 ml-4',
            iconClassName || (accentColor ? iconBgClasses[accentColor] : 'bg-primary/10')
          ]"
        >
          <component
            :is="icon"
            :class="[
              'h-7 w-7',
              iconClassName ? '' : (accentColor ? iconColorClasses[accentColor] : 'text-primary')
            ]"
          />
        </div>
      </div>
    </CardContent>
  </Card>
</template>
