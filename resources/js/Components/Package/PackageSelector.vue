<script setup lang="ts">
import { computed } from 'vue'
import { Card, CardContent, Badge } from '@/Components/ui'
import { Clock, Check } from 'lucide-vue-next'

const props = defineProps<{
  packages: Array<{
    id: number
    name: string
    price: number
    duration: number // in minutes
    description?: string
    isPopular?: boolean
    color?: string
  }>
  selectedPackageId?: number | null
}>()

const emit = defineEmits<{
  select: [packageId: number]
}>()

const formatPrice = (price: number) => {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
  }).format(price)
}

const formatDuration = (minutes: number) => {
  if (minutes < 60) {
    return `${minutes}min`
  }
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return mins > 0 ? `${hours}h ${mins}min` : `${hours}h`
}
</script>

<template>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
    <Card
      v-for="pkg in packages"
      :key="pkg.id"
      variant="interactive"
      :class="[
        'relative cursor-pointer transition-all duration-200',
        selectedPackageId === pkg.id
          ? 'ring-2 ring-primary shadow-lg scale-[1.02]'
          : 'hover:shadow-md',
      ]"
      @click="emit('select', pkg.id)"
    >
      <!-- Popular Badge -->
      <div v-if="pkg.isPopular" class="absolute -top-2 -right-2 z-10">
        <Badge variant="default" class="shadow-md">Most Popular</Badge>
      </div>

      <!-- Selected Checkmark -->
      <div
        v-if="selectedPackageId === pkg.id"
        class="absolute top-3 right-3 w-6 h-6 rounded-full bg-primary flex items-center justify-center animate-scale-in"
      >
        <Check class="h-4 w-4 text-primary-foreground" />
      </div>

      <CardContent class="p-5 space-y-4">
        <!-- Package Name & Price -->
        <div>
          <h4 class="font-bold text-lg mb-1">{{ pkg.name }}</h4>
          <div class="flex items-baseline gap-1">
            <span class="text-3xl font-bold tabular-nums">{{ formatPrice(pkg.price) }}</span>
          </div>
        </div>

        <!-- Duration -->
        <div class="flex items-center gap-2 text-sm text-muted-foreground">
          <Clock class="h-4 w-4 text-primary" />
          <span class="font-mono tabular-nums font-semibold">{{ formatDuration(pkg.duration) }}</span>
        </div>

        <!-- Description -->
        <p
          v-if="pkg.description"
          class="text-sm text-muted-foreground line-clamp-2"
        >
          {{ pkg.description }}
        </p>

        <!-- Color Indicator (if provided) -->
        <div v-if="pkg.color" class="flex items-center gap-2">
          <div
            class="w-4 h-4 rounded-full border-2 border-border"
            :style="{ backgroundColor: pkg.color }"
          />
          <span class="text-xs text-muted-foreground">Package color</span>
        </div>
      </CardContent>
    </Card>
  </div>
</template>
