<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, Badge, Button } from '@/Components/ui'
import { Car, Calendar, Plus, Eye } from 'lucide-vue-next'

const props = defineProps<{
  id: number
  name: string
  email?: string
  phone?: string
  membershipTier?: 'regular' | 'silver' | 'gold' | 'platinum'
  vehicleInfo?: {
    make: string
    model: string
    plate: string
  }
  washHistoryCount?: number
  lastVisit?: string
}>()

const emit = defineEmits<{
  addToQueue: []
  viewHistory: []
}>()

// Generate initials from name
const initials = computed(() => {
  return props.name
    .split(' ')
    .map(n => n[0])
    .join('')
    .toUpperCase()
    .slice(0, 2)
})

// Tier badge variant
const tierBadgeVariant = computed(() => {
  if (!props.membershipTier || props.membershipTier === 'regular') return 'tier-regular'
  return `tier-${props.membershipTier}` as const
})

// Tier color for avatar
const tierAvatarColor = computed(() => {
  switch (props.membershipTier) {
    case 'platinum':
      return 'bg-tier-platinum text-tier-platinum-foreground'
    case 'gold':
      return 'bg-tier-gold text-tier-gold-foreground'
    case 'silver':
      return 'bg-tier-silver text-tier-silver-foreground'
    default:
      return 'bg-tier-regular text-tier-regular-foreground'
  }
})
</script>

<template>
  <Card variant="interactive" class="group">
    <CardContent class="p-5 space-y-4">
      <!-- Header: Avatar + Name + Tier -->
      <div class="flex items-start gap-3">
        <!-- Avatar Circle with Initials -->
        <div
          :class="[
            'w-12 h-12 rounded-full flex items-center justify-center font-bold text-lg flex-shrink-0',
            tierAvatarColor
          ]"
        >
          {{ initials }}
        </div>

        <!-- Name & Tier -->
        <div class="flex-1 min-w-0">
          <h4 class="font-semibold text-base truncate">{{ name }}</h4>
          <Badge :variant="tierBadgeVariant" class="mt-1">
            {{ membershipTier ? membershipTier.charAt(0).toUpperCase() + membershipTier.slice(1) : 'Regular' }}
          </Badge>
        </div>
      </div>

      <!-- Vehicle Info -->
      <div v-if="vehicleInfo" class="flex items-center gap-2 text-sm text-muted-foreground">
        <Car class="h-4 w-4 flex-shrink-0 text-primary" />
        <div class="flex-1 min-w-0">
          <p class="truncate">{{ vehicleInfo.make }} {{ vehicleInfo.model }}</p>
          <p class="text-xs font-mono">{{ vehicleInfo.plate }}</p>
        </div>
      </div>

      <!-- Stats: Wash Count + Last Visit -->
      <div class="flex items-center justify-between text-xs text-muted-foreground pt-2 border-t border-border">
        <div v-if="washHistoryCount !== undefined" class="flex items-center gap-1">
          <span class="font-semibold text-foreground tabular-nums">{{ washHistoryCount }}</span>
          <span>{{ washHistoryCount === 1 ? 'wash' : 'washes' }}</span>
        </div>
        <div v-if="lastVisit" class="flex items-center gap-1">
          <Calendar class="h-3 w-3" />
          <span>{{ lastVisit }}</span>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="flex gap-2">
        <Button
          variant="primary-action"
          size="sm"
          class="flex-1"
          @click="emit('addToQueue')"
        >
          <Plus class="h-4 w-4" />
          Add to Queue
        </Button>
        <Button
          variant="outline"
          size="sm"
          @click="emit('viewHistory')"
        >
          <Eye class="h-4 w-4" />
          History
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
