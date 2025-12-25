<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, Badge, Button } from '@/Components/ui'
import { Car, Clock, Package, X, Play } from 'lucide-vue-next'

const props = defineProps<{
  position: number
  customerName: string
  vehicleInfo: {
    make?: string
    model?: string
    plate: string
  }
  packageName: string
  estimatedDuration?: string
  waitTime?: number // in minutes
  status: 'waiting' | 'in-progress'
  paymentStatus?: string
  hasPackage?: boolean
}>()

const emit = defineEmits<{
  start: []
  complete: []
  remove: []
  edit: []
}>()

const waitTimeConfig = computed(() => {
  if (!props.waitTime) return null

  if (props.waitTime < 10) {
    return { color: 'text-success', label: `${props.waitTime}min wait` }
  } else if (props.waitTime < 20) {
    return { color: 'text-warning', label: `${props.waitTime}min wait` }
  } else {
    return { color: 'text-destructive', label: `${props.waitTime}min wait` }
  }
})

const statusBadgeVariant = computed(() => {
  return props.status === 'waiting' ? 'queue-waiting' : 'queue-in-progress'
})
</script>

<template>
  <Card
    variant="operational"
    :class="[
      'transition-all duration-200 border-l-4',
      status === 'waiting' ? 'border-queue-waiting' : 'border-queue-in-progress',
      status === 'in-progress' && 'bg-queue-in-progress/5',
      'animate-slide-in-right'
    ]"
  >
    <CardContent class="p-4">
      <div class="flex items-start gap-4">
        <!-- Large Queue Position Number -->
        <div class="flex-shrink-0">
          <div class="text-5xl font-bold font-mono tabular-nums text-primary">
            {{ position }}
          </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 min-w-0 space-y-2">
          <!-- Customer Name & Status -->
          <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
              <h4 class="font-semibold text-base truncate">{{ customerName }}</h4>
            </div>
            <Badge :variant="statusBadgeVariant" class="flex-shrink-0">
              {{ status === 'waiting' ? 'Waiting' : 'In Progress' }}
            </Badge>
          </div>

          <!-- Vehicle Info -->
          <div class="flex items-center gap-2 text-sm text-muted-foreground">
            <Car class="h-4 w-4 flex-shrink-0" />
            <span class="truncate">
              {{ vehicleInfo.make }} {{ vehicleInfo.model }} • {{ vehicleInfo.plate }}
            </span>
          </div>

          <!-- Package & Duration -->
          <div class="flex items-center justify-between gap-2 text-sm">
            <div class="flex items-center gap-2 text-muted-foreground">
              <Package class="h-4 w-4 flex-shrink-0" />
              <span>{{ packageName }}</span>
            </div>
            <div v-if="estimatedDuration" class="flex items-center gap-1 font-mono tabular-nums text-xs text-muted-foreground">
              <Clock class="h-3 w-3" />
              <span>{{ estimatedDuration }}</span>
            </div>
          </div>

          <!-- Wait Time Indicator -->
          <div v-if="waitTimeConfig" :class="['text-xs font-semibold', waitTimeConfig.color]">
            {{ waitTimeConfig.label }}
          </div>
        </div>

        <!-- Actions (Desktop) -->
        <div class="hidden sm:flex flex-col gap-2">
          <Button
            v-if="status === 'waiting'"
            variant="primary-action"
            size="sm"
            @click="emit('start')"
            :disabled="paymentStatus !== 'paid' || !hasPackage"
            :title="paymentStatus !== 'paid' ? 'Payment must be confirmed first' : (!hasPackage ? 'Package must be assigned first' : '')"
          >
            <Play class="h-4 w-4" />
            Assign Bay
          </Button>
          <Button
            v-if="status === 'in-progress'"
            variant="primary-action"
            size="sm"
            @click="emit('complete')"
          >
            <CheckCircle class="h-4 w-4" />
            Complete
          </Button>
          <Button
            variant="outline"
            size="sm"
            @click="emit('remove')"
          >
            <X class="h-4 w-4" />
            Remove
          </Button>
        </div>
      </div>

      <!-- Actions (Mobile) -->
      <div class="flex sm:hidden gap-2 mt-3 pt-3 border-t border-border">
        <Button
          v-if="status === 'waiting'"
          variant="primary-action"
          size="sm"
          class="flex-1"
          @click="emit('start')"
          :disabled="paymentStatus !== 'paid' || !hasPackage"
          :title="paymentStatus !== 'paid' ? 'Payment must be confirmed first' : (!hasPackage ? 'Package must be assigned first' : '')"
        >
          <Play class="h-4 w-4" />
          Assign
        </Button>
        <Button
          v-if="status === 'in-progress'"
          variant="primary-action"
          size="sm"
          class="flex-1"
          @click="emit('complete')"
        >
          <CheckCircle class="h-4 w-4" />
          Complete
        </Button>
        <Button
          variant="outline"
          size="sm"
          class="flex-1"
          @click="emit('remove')"
        >
          <X class="h-4 w-4" />
          Remove
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
