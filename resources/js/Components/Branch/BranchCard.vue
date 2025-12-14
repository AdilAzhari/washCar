<script setup lang="ts">
import { Button, Badge } from '@/Components/ui'
import { Building2, MapPin, Phone, Clock, QrCode, Edit, Trash2, Eye } from 'lucide-vue-next'
import { router } from '@inertiajs/vue3'

interface Branch {
  id: number
  name: string
  code: string
  is_active: boolean
  address?: string
  phone?: string
  operating_hours?: string
  bays_count?: number
  washes_count?: number
}

const props = defineProps<{
  branch: Branch
}>()

const emit = defineEmits<{
  edit: [branch: Branch]
  delete: [id: number]
}>()

const viewBranch = () => {
  router.visit(`/branches/${props.branch.id}`)
}
</script>

<template>
  <div class="stat-card hover-lift group">
    <!-- Header -->
    <div class="flex items-start justify-between mb-4">
      <div class="flex-1">
        <div class="flex items-center gap-2 mb-1">
          <h3 class="font-semibold text-lg">{{ branch.name }}</h3>
          <Badge
            :variant="branch.is_active ? 'default' : 'secondary'"
            :class="branch.is_active ? 'bg-success/10 text-success hover:bg-success/20' : 'bg-muted text-muted-foreground'"
          >
            {{ branch.is_active ? 'Active' : 'Inactive' }}
          </Badge>
        </div>
        <p class="text-sm text-muted-foreground font-mono">{{ branch.code }}</p>
      </div>
      <div class="flex items-center gap-1">
        <Button
          size="sm"
          variant="ghost"
          @click="viewBranch"
          class="opacity-0 group-hover:opacity-100 transition-opacity"
        >
          View
        </Button>
      </div>
    </div>

    <!-- Info -->
    <div class="space-y-2 mb-4">
      <div v-if="branch.address" class="flex items-start gap-2 text-sm">
        <MapPin class="w-4 h-4 text-muted-foreground mt-0.5 shrink-0" />
        <span class="text-muted-foreground">{{ branch.address }}</span>
      </div>
      <div v-if="branch.phone" class="flex items-center gap-2 text-sm">
        <Phone class="w-4 h-4 text-muted-foreground" />
        <span class="text-muted-foreground">{{ branch.phone }}</span>
      </div>
      <div v-if="branch.operating_hours" class="flex items-center gap-2 text-sm">
        <Clock class="w-4 h-4 text-muted-foreground" />
        <span class="text-muted-foreground">{{ branch.operating_hours }}</span>
      </div>
      <div class="flex items-center gap-2 text-sm">
        <Building2 class="w-4 h-4 text-muted-foreground" />
        <span class="text-muted-foreground">{{ branch.bays_count || 0 }} Bays</span>
      </div>
    </div>

    <!-- Stats -->
    <div class="bg-muted/50 rounded-lg p-3 mb-4 space-y-2">
      <div class="flex items-center justify-between text-sm">
        <span class="text-muted-foreground">Total Washes:</span>
        <span class="font-semibold">{{ branch.washes_count || 0 }}</span>
      </div>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-2">
      <Button size="sm" variant="outline" class="flex-1" @click="viewBranch">
        <Eye class="w-4 h-4 mr-1" />
        View
      </Button>
      <Button size="sm" variant="outline" @click="$emit('edit', branch)">
        <Edit class="w-4 h-4" />
      </Button>
      <Button size="sm" variant="outline" @click="$emit('delete', branch.id)">
        <Trash2 class="w-4 h-4" />
      </Button>
    </div>
  </div>
</template>
