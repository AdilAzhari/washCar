<script setup lang="ts">
import { computed } from 'vue'
import { Head, router, Link, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Card, CardContent, CardHeader, CardTitle, Badge, Button } from '@/Components/ui'
import { Play, X, Clock, CheckCircle, ArrowRight } from 'lucide-vue-next'

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

const props = defineProps<{
  waitingQueue: QueueEntry[]
  stats: {
    totalWaiting: number
    averageWaitTime: number
    availableBays: number
  }
}>()

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const startWash = (queueId: number) => {
  router.post(route(getRouteName('queue.start'), queueId), {}, {
    preserveScroll: true
  })
}

const cancelQueue = (queueId: number) => {
  if (confirm('Cancel this queue entry?')) {
    router.post(route(getRouteName('queue.cancel'), queueId), {}, {
      preserveScroll: true
    })
  }
}

const confirmPayment = (queueId: number) => {
  router.post(route(getRouteName('queue.confirm-payment'), queueId), {}, {
    preserveScroll: true
  })
}

const formatTime = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}
</script>

<template>
  <Head title="Waiting Queue" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Waiting Queue</h2>
        <Link :href="route(getRouteName('queue.in-progress'))">
          <Button variant="outline" size="sm">
            In Progress
            <ArrowRight class="w-4 h-4 ml-2" />
          </Button>
        </Link>
      </div>
    </template>
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6 animate-fade-in">
          <!-- Header -->
          <div>
            <h1 class="text-2xl font-bold mb-2">Waiting Queue</h1>
            <p class="text-muted-foreground">Customers waiting to be served</p>
          </div>

          <!-- Stats Grid -->
          <div class="grid gap-4 md:grid-cols-3">
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

          <!-- Waiting Queue List -->
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
                          {{ entry.package.name }} ({{ entry.package.duration_minutes }} min) - RM {{ entry.package.price }}
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
      </div>
    </div>
  </AuthenticatedLayout>
</template>
