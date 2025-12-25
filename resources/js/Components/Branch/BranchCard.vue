<script setup lang="ts">
import { Card, CardContent, Button, Badge } from '@/Components/ui'
import { Building2, MapPin, Phone, Clock, QrCode, Edit, Trash2, Eye, Droplet } from 'lucide-vue-next'
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
  router.visit(route('admin.branches.show', props.branch.id))
}
</script>

<template>
  <Card variant="interactive" class="group overflow-hidden">
    <!-- Status Accent Bar -->
    <div :class="['h-1', branch.is_active ? 'bg-success' : 'bg-muted']" />

    <CardContent class="p-6 space-y-4">
      <!-- Header: Branch Name + Status -->
      <div class="flex items-start justify-between gap-3">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
            <Building2 class="h-5 w-5 text-primary flex-shrink-0" />
            <h3 class="font-bold text-xl truncate">{{ branch.name }}</h3>
          </div>
          <p class="text-sm text-muted-foreground font-mono">{{ branch.code }}</p>
        </div>
        <Badge :variant="branch.is_active ? 'default' : 'secondary'">
          {{ branch.is_active ? 'Active' : 'Inactive' }}
        </Badge>
      </div>

      <!-- Contact Info -->
      <div class="space-y-2 text-sm">
        <div v-if="branch.address" class="flex items-start gap-2">
          <MapPin class="w-4 h-4 text-primary mt-0.5 flex-shrink-0" />
          <span class="text-muted-foreground line-clamp-2">{{ branch.address }}</span>
        </div>
        <div v-if="branch.phone" class="flex items-center gap-2">
          <Phone class="w-4 h-4 text-primary flex-shrink-0" />
          <span class="text-muted-foreground">{{ branch.phone }}</span>
        </div>
        <div v-if="branch.operating_hours" class="flex items-center gap-2">
          <Clock class="w-4 h-4 text-primary flex-shrink-0" />
          <span class="text-muted-foreground">{{ branch.operating_hours }}</span>
        </div>
      </div>

      <!-- Stats Grid -->
      <div class="grid grid-cols-2 gap-3 pt-2 border-t border-border">
        <!-- Bays Count -->
        <div class="text-center p-3 bg-bay-active/5 rounded-lg border border-bay-active/20">
          <div class="flex items-center justify-center gap-1 mb-1">
            <Droplet class="h-4 w-4 text-bay-active" />
            <span class="text-2xl font-bold tabular-nums text-bay-active">{{ branch.bays_count || 0 }}</span>
          </div>
          <p class="text-xs text-muted-foreground">Bays</p>
        </div>

        <!-- Washes Count -->
        <div class="text-center p-3 bg-success/5 rounded-lg border border-success/20">
          <div class="text-2xl font-bold tabular-nums text-success mb-1">{{ branch.washes_count || 0 }}</div>
          <p class="text-xs text-muted-foreground">Washes</p>
        </div>
      </div>

      <!-- QR Code Action (if branch has a queue system) -->
      <Button
        v-if="branch.is_active"
        variant="outline"
        size="sm"
        class="w-full"
        @click="router.visit(route('admin.branches.qrcode', branch.id))"
      >
        <QrCode class="h-4 w-4" />
        View QR Code
      </Button>

      <!-- Actions -->
      <div class="flex items-center gap-2">
        <Button size="sm" variant="primary-action" class="flex-1" @click="viewBranch">
          <Eye class="w-4 h-4" />
          View
        </Button>
        <Button size="sm" variant="outline" @click="$emit('edit', branch)">
          <Edit class="w-4 h-4" />
        </Button>
        <Button size="sm" variant="outline" @click="$emit('delete', branch.id)">
          <Trash2 class="w-4 h-4" />
        </Button>
      </div>
    </CardContent>
  </Card>
</template>
