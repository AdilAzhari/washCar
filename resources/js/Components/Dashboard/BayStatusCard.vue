<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, Badge, Button } from '@/Components/ui'
import Progress from '@/Components/ui/Progress.vue'
import { Play, CheckCircle, Wrench, Droplet, Pencil, Trash2 } from 'lucide-vue-next'

const props = defineProps<{
  bayNumber: string
  status: 'idle' | 'active' | 'maintenance' | 'completed'
  currentWash?: {
    customerName: string
    vehiclePlate: string
    packageName: string
    progress: number
    elapsedTime?: string
    estimatedTime?: string
  }
  queueCount?: number
}>()

const emit = defineEmits<{
  start: []
  complete: []
  maintenance: []
  edit: []
  delete: []
  statusChange: [status: string]
}>()

const statusConfig = computed(() => {
  switch (props.status) {
    case 'active':
      return {
        variant: 'bay-active' as const,
        label: 'Active',
        borderColor: 'border-bay-active',
        bgColor: 'bg-bay-active/5',
      }
    case 'maintenance':
      return {
        variant: 'bay-maintenance' as const,
        label: 'Maintenance',
        borderColor: 'border-bay-maintenance',
        bgColor: 'bg-bay-maintenance/5',
      }
    case 'completed':
      return {
        variant: 'bay-completed' as const,
        label: 'Completed',
        borderColor: 'border-bay-completed',
        bgColor: 'bg-bay-completed/5',
      }
    default:
      return {
        variant: 'bay-idle' as const,
        label: 'Idle',
        borderColor: 'border-bay-idle',
        bgColor: 'bg-bay-idle/5',
      }
  }
})
</script>

<template>
  <Card
    variant="operational"
    :class="[
      'transition-all duration-300 border-l-4',
      statusConfig.borderColor,
      statusConfig.bgColor,
      status === 'active' && 'animate-fade-in-fast'
    ]"
  >
    <CardContent class="p-5 min-h-[320px] flex flex-col">
      <div class="space-y-4 flex-1">
        <!-- Bay Number & Status -->
        <div class="flex items-start justify-between gap-2">
          <div class="flex items-baseline gap-3">
            <h3 class="text-5xl font-bold font-mono tracking-tight">{{ bayNumber }}</h3>
            <Droplet class="h-5 w-5 text-primary mt-2 flex-shrink-0" />
          </div>
          <Badge :variant="statusConfig.variant" class="text-xs flex-shrink-0 whitespace-nowrap">
            {{ statusConfig.label }}
          </Badge>
        </div>

        <!-- Active Wash Info -->
        <div v-if="currentWash && status === 'active'" class="space-y-3 animate-fade-in-fast">
          <div>
            <p class="font-semibold text-base truncate">{{ currentWash.customerName }}</p>
            <p class="text-sm text-muted-foreground">{{ currentWash.vehiclePlate }}</p>
          </div>

          <!-- Progress Bar -->
          <div class="space-y-2">
            <div class="flex justify-between items-center text-sm">
              <span class="text-muted-foreground">{{ currentWash.packageName }}</span>
              <span class="font-mono font-semibold tabular-nums">{{ currentWash.progress }}%</span>
            </div>
            <Progress :model-value="currentWash.progress" class="h-2.5" />
          </div>

          <!-- Timer -->
          <div v-if="currentWash.elapsedTime" class="flex justify-between items-center text-sm">
            <span class="font-mono tabular-nums text-muted-foreground">
              {{ currentWash.elapsedTime }}
            </span>
            <span v-if="currentWash.estimatedTime" class="font-mono tabular-nums text-xs text-muted-foreground">
              Est: {{ currentWash.estimatedTime }}
            </span>
          </div>
        </div>

        <!-- Idle State -->
        <div v-else-if="status === 'idle'" class="py-6 text-center">
          <p class="text-sm text-muted-foreground">Bay ready for service</p>
          <p v-if="queueCount && queueCount > 0" class="text-xs text-muted-foreground mt-1">
            {{ queueCount }} in queue
          </p>
        </div>

        <!-- Maintenance State -->
        <div v-else-if="status === 'maintenance'" class="py-4 text-center">
          <Wrench class="h-8 w-8 mx-auto text-bay-maintenance mb-2" />
          <p class="text-sm font-medium text-bay-maintenance">Under Maintenance</p>
          <p class="text-xs text-muted-foreground mt-1">Bay unavailable</p>
        </div>

        <!-- Completed State -->
        <div v-else-if="status === 'completed' && currentWash" class="py-4 text-center animate-scale-success">
          <CheckCircle class="h-8 w-8 mx-auto text-bay-completed mb-2" />
          <p class="text-sm font-medium">Wash Complete</p>
          <p class="text-xs text-muted-foreground mt-1">{{ currentWash.customerName }}</p>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="space-y-2 pt-2 border-t border-border">
        <!-- Primary Actions -->
        <div class="flex gap-2">
          <Button
            v-if="status === 'idle'"
            variant="primary-action"
            size="sm"
            class="flex-1"
            @click="emit('start')"
          >
            <Play class="h-4 w-4" />
            Start
          </Button>
          <Button
            v-if="status === 'active'"
            variant="primary-action"
            size="sm"
            class="flex-1"
            @click="emit('complete')"
          >
            <CheckCircle class="h-4 w-4" />
            Complete
          </Button>
          <Button
            variant="danger-action"
            size="sm"
            :class="status === 'active' || status === 'idle' ? 'w-auto px-3' : 'flex-1'"
            @click="emit('maintenance')"
          >
            <Wrench class="h-4 w-4" />
            <span class="truncate">{{ status === 'maintenance' ? 'Resume' : 'Maint.' }}</span>
          </Button>
        </div>

        <!-- Secondary Actions -->
        <div class="flex gap-2">
          <Button
            variant="outline"
            size="sm"
            class="flex-1"
            @click="emit('edit')"
          >
            <Pencil class="h-4 w-4" />
            Edit
          </Button>
          <Button
            variant="outline"
            size="sm"
            class="flex-1"
            @click="emit('delete')"
          >
            <Trash2 class="h-4 w-4" />
            Delete
          </Button>
        </div>
      </div>
    </CardContent>
  </Card>
</template>
