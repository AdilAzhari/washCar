<script setup lang="ts">
import { ref, computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import QueueFormModal from '@/Components/Queue/QueueFormModal.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import { Plus, Clock, CheckCircle, XCircle } from 'lucide-vue-next'

interface QueueEntry {
  id: number
  branch: { id: number; name: string }
  customer?: { id: number; name: string }
  package?: { id: number; name: string; price: number }
  plate_number: string
  position: number
  status: string
  joined_at: string
}

const props = defineProps<{
  queueEntries: QueueEntry[]
  branches: any[]
  customers: any[]
  packages: any[]
}>()

const isFormOpen = ref(false)

const stats = computed(() => ({
  total: props.queueEntries.length,
  waiting: props.queueEntries.filter(q => q.status === 'waiting').length,
  inProgress: props.queueEntries.filter(q => q.status === 'in_progress').length,
  completed: props.queueEntries.filter(q => q.status === 'completed').length,
}))

const updateStatus = (id: number, status: string) => {
  router.patch(route('queue.update', id), { status }, { preserveScroll: true })
}

const getStatusVariant = (status: string) => {
  switch (status) {
    case 'waiting': return 'default'
    case 'in_progress': return 'secondary'
    case 'completed': return 'default'
    case 'cancelled': return 'destructive'
    default: return 'default'
  }
}
</script>

<template>
  <Head title="Queue Management" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">Queue Management</h2>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold mb-2">Queue Management</h1>
              <p class="text-muted-foreground">Track and manage customer queue</p>
            </div>
            <Button class="btn-primary" @click="isFormOpen = true">
              <Plus class="w-4 h-4 mr-2" />
              Add to Queue
            </Button>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Total</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.total }}</p>
                  </div>
                  <Clock class="w-8 h-8 text-primary" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Waiting</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.waiting }}</p>
                  </div>
                  <Clock class="w-8 h-8 text-warning" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">In Progress</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.inProgress }}</p>
                  </div>
                  <Clock class="w-8 h-8 text-accent" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Completed</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.completed }}</p>
                  </div>
                  <CheckCircle class="w-8 h-8 text-success" />
                </div>
              </CardContent>
            </Card>
          </div>

          <Card>
            <CardHeader>
              <CardTitle>Queue Entries</CardTitle>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <div v-for="entry in queueEntries" :key="entry.id" class="flex items-center justify-between p-4 border rounded-lg">
                  <div class="flex-1">
                    <div class="flex items-center gap-4">
                      <div class="text-2xl font-bold text-muted-foreground">#{{ entry.position }}</div>
                      <div>
                        <p class="font-medium">{{ entry.plate_number }}</p>
                        <p class="text-sm text-muted-foreground">
                          {{ entry.customer?.name || 'Walk-in' }} • {{ entry.branch.name }}
                        </p>
                        <p v-if="entry.package" class="text-sm text-muted-foreground">{{ entry.package.name }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="flex items-center gap-2">
                    <Badge :variant="getStatusVariant(entry.status)" class="capitalize">{{ entry.status.replace('_', ' ') }}</Badge>
                    <Button v-if="entry.status === 'waiting'" size="sm" @click="updateStatus(entry.id, 'in_progress')">Start</Button>
                    <Button v-if="entry.status === 'in_progress'" size="sm" @click="updateStatus(entry.id, 'completed')">Complete</Button>
                  </div>
                </div>
              </div>
            </CardContent>
          </Card>

          <QueueFormModal
            :is-open="isFormOpen"
            :branches="branches"
            :customers="customers"
            :packages="packages"
            @close="isFormOpen = false"
          />
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
