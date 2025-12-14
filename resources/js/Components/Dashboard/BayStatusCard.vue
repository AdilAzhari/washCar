<script setup lang="ts">
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import Progress from '@/Components/ui/Progress.vue'
import { Users, Clock } from 'lucide-vue-next'

defineProps<{
  bayName: string
  isOperational: boolean
  currentWash?: {
    customerName: string
    vehiclePlate: string
    packageName: string
    packageColor: string
    progress: number
    elapsedTime?: string
    estimatedTime?: string
  }
  queueCount: number
}>()
</script>

<template>
  <Card class="hover-lift border-border/50">
    <CardHeader class="pb-3">
      <div class="flex items-center justify-between">
        <CardTitle class="text-lg font-semibold">{{ bayName }}</CardTitle>
        <Badge :variant="isOperational ? 'default' : 'destructive'" class="badge-status">
          {{ isOperational ? 'Operational' : 'Out of Service' }}
        </Badge>
      </div>
    </CardHeader>
    <CardContent class="space-y-4">
      <div v-if="currentWash" class="space-y-2">
        <div class="flex justify-between items-start">
          <div>
            <p class="font-medium text-sm">{{ currentWash.customerName }}</p>
            <p class="text-xs text-muted-foreground">{{ currentWash.vehiclePlate }}</p>
          </div>
          <Badge class="badge-status" :style="{ backgroundColor: currentWash.packageColor }">
            {{ currentWash.packageName }}
          </Badge>
        </div>

        <div class="space-y-1">
          <div class="flex justify-between text-xs text-muted-foreground">
            <span>Progress</span>
            <span>{{ currentWash.progress }}%</span>
          </div>
          <Progress :model-value="currentWash.progress" class="h-2" />
        </div>

        <div v-if="currentWash.elapsedTime" class="flex items-center justify-between text-xs text-muted-foreground">
          <div class="flex items-center gap-1">
            <Clock class="h-3 w-3" />
            <span>{{ currentWash.elapsedTime }} elapsed</span>
          </div>
          <span v-if="currentWash.estimatedTime">Est: {{ currentWash.estimatedTime }}</span>
        </div>
      </div>

      <div v-else class="py-4 text-center text-sm text-muted-foreground">
        No active wash
      </div>

      <div class="flex items-center justify-between pt-2 border-t border-border">
        <div class="flex items-center gap-2 text-sm">
          <Users class="h-4 w-4 text-muted-foreground" />
          <span class="text-muted-foreground">Queue:</span>
          <span class="font-semibold">{{ queueCount }}</span>
        </div>
        <Button variant="outline" size="sm">
          View Queue
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
