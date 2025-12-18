<script setup lang="ts">
import { Head, router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import { X, Activity, CheckCircle, ArrowLeft } from 'lucide-vue-next'

interface Wash {
  id: number
  branch: { id: number; name: string }
  customer?: { id: number; name: string }
  package?: { id: number; name: string; duration_minutes: number; price: number }
  bay: { id: number; name: string }
  plate_number?: string
  status: string
  started_at: string
  queue_entry: {
    id: number
    payment_status: string
  } | null
}

const props = defineProps<{
  inProgressWashes: Wash[]
  stats: {
    inProgress: number
    activeBays: number
  }
}>()

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

const confirmPayment = (queueId: number) => {
  router.post(route('queue.confirm-payment', queueId), {}, {
    preserveScroll: true
  })
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
</script>

<template>
  <Head title="In Progress" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">In Progress</h2>
        <Link :href="route('queue.waiting')">
          <Button variant="outline" size="sm">
            <ArrowLeft class="w-4 h-4 mr-2" />
            Waiting Queue
          </Button>
        </Link>
      </div>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div>
            <h1 class="text-2xl font-bold mb-2">In Progress</h1>
            <p class="text-muted-foreground">Active washes currently being served</p>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-2">
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
                    <p class="text-sm text-muted-foreground">Active Bays</p>
                    <p class="text-3xl font-bold mt-1">{{ stats.activeBays }}</p>
                  </div>
                  <CheckCircle class="w-8 h-8 text-success" />
                </div>
              </CardContent>
            </Card>
          </div>

          <!-- In Progress List -->
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

                  <div v-if="wash.package" class="space-y-1">
                    <div class="flex justify-between text-sm">
                      <div class="flex items-center gap-2">
                        <span>{{ wash.package.name }} - ${{ wash.package.price }}</span>
                        <Badge
                          v-if="wash.queue_entry"
                          :class="wash.queue_entry.payment_status === 'paid' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'"
                        >
                          {{ wash.queue_entry.payment_status === 'paid' ? 'Paid' : 'Pending' }}
                        </Badge>
                      </div>
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
                    <Button
                      v-if="wash.queue_entry && wash.package && wash.queue_entry.payment_status === 'pending'"
                      size="sm"
                      variant="outline"
                      class="bg-green-50 hover:bg-green-100 border-green-300"
                      @click="confirmPayment(wash.queue_entry.id)"
                    >
                      💳 Confirm Payment
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
  </AuthenticatedLayout>
</template>
