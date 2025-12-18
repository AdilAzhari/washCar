<script setup lang="ts">
import { computed } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import { Play, X, Clock, Activity, CheckCircle } from 'lucide-vue-next'

interface QueueEntry {
  id: number
  branch: { id: number; name: string }
  customer?: { id: number; name: string }
  package?: { id: number; name: string; duration_minutes: number; price: number }
  plate_number: string
  position: number
  status: string
  payment_status: string
  joined_at: string
}

interface Wash {
  id: number
  branch: { id: number; name: string }
  customer?: { id: number; name: string }
  package?: { id: number; name: string; duration_minutes: number; price: number }
  bay: { id: number; name: string }
  plate_number?: string
  status: string
  payment_status?: string
  started_at: string
  queue_entry_id?: number
}

const props = defineProps<{
  waitingQueue: QueueEntry[]
  inProgressWashes: Wash[]
  stats: {
    totalWaiting: number
    inProgress: number
    averageWaitTime: number
    availableBays: number
  }
}>()

const startWash = (queueId: number) => {
  router.post(route('queue.start', queueId), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Optionally refresh the page
    }
  })
}

const cancelQueue = (queueId: number) => {
  if (confirm('Cancel this queue entry?')) {
    router.post(route('queue.cancel', queueId), {}, {
      preserveScroll: true
    })
  }
}

const completeWash = (washId: number) => {
  router.post(route('wash.complete', washId), {}, {
    preserveScroll: true
  })
}

const cancelWash = (washId: number) => {
  if (confirm('Cancel this wash?')) {
    router.post(route('wash.cancel', washId), {}, {
      preserveScroll: true
    })
  }
}

const formatTime = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const getElapsedTime = (startedAt: string) => {
  const start = new Date(startedAt)
  const now = new Date()
  const diffMs = now.getTime() - start.getTime()
  const diffMins = Math.floor(diffMs / 60000)
  return `${diffMins} min`
}

const getProgress = (wash: Wash) => {
  if (!wash.package?.duration_minutes) return 0
  const start = new Date(wash.started_at)
  const now = new Date()
  const diffMs = now.getTime() - start.getTime()
  const diffMins = diffMs / 60000
  const progress = (diffMins / wash.package.duration_minutes) * 100
  return Math.min(Math.round(progress), 100)
}

const confirmPayment = (queueId: number) => {
  router.post(route('queue.confirm-payment', queueId), {}, {
    preserveScroll: true
  })
}
</script>

<template>
  <Head title="View Queue" />
  <AuthenticatedLayout>
    <template #header>
      <h2 class="font-semibold text-xl text-gray-800 leading-tight">View Queue</h2>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div>
            <h1 class="text-2xl font-bold mb-2">Queue Management</h1>
            <p class="text-muted-foreground">Monitor and manage active wash queue</p>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-4">
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Total Waiting</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.totalWaiting }}</p>
                  </div>
                  <Clock class="w-8 h-8 text-primary" />
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
                  <Activity class="w-8 h-8 text-accent" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Avg Wait Time</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.averageWaitTime }}</p>
                    <p class="text-xs text-muted-foreground">minutes</p>
                  </div>
                  <Clock class="w-8 h-8 text-warning" />
                </div>
              </CardContent>
            </Card>
            <Card class="stat-card">
              <CardContent class="p-6">
                <div class="flex items-center justify-between">
                  <div>
                    <p class="text-sm text-muted-foreground">Available Bays</p>
                    <p class="text-3xl font-bold mt-1 text-success">{{ stats.availableBays }}</p>
                  </div>
                  <CheckCircle class="w-8 h-8 text-success" />
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- Two Column Layout -->
          <div class="grid gap-6 md:grid-cols-2">
            <!-- Waiting Queue -->
            <div class="space-y-4">
              <h2 class="text-xl font-bold">Waiting Queue</h2>
              <div class="space-y-3">
                <Card
                  v-for="entry in waitingQueue"
                  :key="entry.id"
                  class="hover-lift bg-warning/5"
                >
                  <CardContent class="p-4">
                    <div class="flex items-start justify-between gap-4">
                      <div class="flex items-start gap-3">
                        <Badge variant="secondary" class="text-lg font-bold px-3">
                          #{{ entry.position }}
                        </Badge>
                        <div>
                          <p class="font-semibold text-lg">{{ entry.plate_number }}</p>
                          <p class="text-sm text-muted-foreground">
                            {{ entry.customer?.name || 'Walk-in' }} • {{ entry.branch.name }}
                          </p>
                          <p class="text-sm text-muted-foreground mt-1">
                            <Clock class="w-3 h-3 inline mr-1" />
                            Joined: {{ formatTime(entry.joined_at) }}
                          </p>
                          <div v-if="entry.package" class="flex items-center gap-2 mt-1">
                            <p class="text-sm font-medium">
                              {{ entry.package.name }} ({{ entry.package.duration_minutes }} min) - ${{ entry.package.price }}
                            </p>
                            <Badge :class="entry.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'">
                              {{ entry.payment_status === 'paid' ? 'Paid' : 'Pending' }}
                            </Badge>
                          </div>
                        </div>
                      </div>
                      <div class="flex flex-col gap-2">
                        <div class="flex gap-2">
                          <Button size="sm" class="btn-primary" @click="startWash(entry.id)">
                            <Play class="w-4 h-4 mr-1" />
                            Start
                          </Button>
                          <Button size="sm" variant="outline" @click="cancelQueue(entry.id)">
                            <X class="w-4 h-4" />
                          </Button>
                        </div>
                        <Button
                          v-if="entry.package && entry.payment_status === 'pending'"
                          size="sm"
                          variant="outline"
                          class="bg-green-50 hover:bg-green-100 border-green-300"
                          @click="confirmPayment(entry.id)"
                        >
                          💳 Confirm Payment
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <Card v-if="waitingQueue.length === 0" class="bg-muted/30">
                  <CardContent class="p-12 text-center">
                    <Clock class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                    <h3 class="text-lg font-semibold mb-2">No one waiting</h3>
                    <p class="text-muted-foreground">The queue is empty</p>
                  </CardContent>
                </Card>
              </div>
            </div>

            <!-- In Progress -->
            <div class="space-y-4">
              <h2 class="text-xl font-bold">In Progress</h2>
              <div class="space-y-3">
                <Card
                  v-for="wash in inProgressWashes"
                  :key="wash.id"
                  class="hover-lift bg-primary/5"
                >
                  <CardContent class="p-4">
                    <div class="space-y-3">
                      <div class="flex items-start justify-between">
                        <div>
                          <p class="font-semibold text-lg">{{ wash.customer?.name || 'Walk-in' }}</p>
                          <p class="text-sm text-muted-foreground">{{ wash.branch.name }}</p>
                          <Badge variant="outline" class="mt-2">
                            Bay {{ wash.bay.name }}
                          </Badge>
                        </div>
                        <div class="text-right">
                          <p class="text-sm text-muted-foreground">Elapsed</p>
                          <p class="text-lg font-bold">{{ getElapsedTime(wash.started_at) }}</p>
                        </div>
                      </div>

                      <div class="space-y-1">
                        <div class="flex justify-between text-sm">
                          <span>Progress</span>
                          <span>{{ getProgress(wash) }}%</span>
                        </div>
                        <div class="w-full bg-secondary rounded-full h-2">
                          <div
                            class="bg-primary h-2 rounded-full transition-all"
                            :style="{ width: `${getProgress(wash)}%` }"
                          ></div>
                        </div>
                      </div>

                      <div class="flex gap-2 pt-2">
                        <Button size="sm" class="btn-primary flex-1" @click="completeWash(wash.id)">
                          <CheckCircle class="w-4 h-4 mr-1" />
                          Complete
                        </Button>
                        <Button size="sm" variant="outline" @click="cancelWash(wash.id)">
                          <X class="w-4 h-4" />
                        </Button>
                      </div>
                    </div>
                  </CardContent>
                </Card>

                <Card v-if="inProgressWashes.length === 0" class="bg-muted/30">
                  <CardContent class="p-12 text-center">
                    <Activity class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                    <h3 class="text-lg font-semibold mb-2">No active washes</h3>
                    <p class="text-muted-foreground">All bays are idle</p>
                  </CardContent>
                </Card>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
