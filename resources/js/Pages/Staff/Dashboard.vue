<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import StaffLayout from '@/Layouts/StaffLayout.vue'
import {
  ListOrdered,
  Waves,
  CheckCircle,
  Clock,
  Calendar,
  Play,
  User,
  Package,
} from 'lucide-vue-next'

defineProps<{
  stats: {
    waitingQueue: number
    activeBays: number
    idleBays: number
    completedToday: number
  }
  queueEntries: Array<{
    id: number
    position: number
    customer: { name: string; phone: string }
    package: { name: string; estimated_duration: number }
    plate_number: string
    vehicle_type: string
    created_at: string
  }>
  activeWashes: Array<{
    id: number
    customer: { name: string }
    package: { name: string }
    bay: { name: string }
    started_at: string
  }>
  bays: Array<{
    id: number
    name: string
    status: string
    currentWash?: {
      customer: { name: string }
      package: { name: string }
      started_at: string
    }
  }>
  todayAppointments: Array<{
    id: number
    scheduled_at: string
    customer: { name: string }
    package: { name: string }
    plate_number: string
    status: string
  }>
}>()

const formatTime = (dateString: string) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
  })
}

const getElapsedMinutes = (startTime: string) => {
  const start = new Date(startTime)
  const now = new Date()
  return Math.floor((now.getTime() - start.getTime()) / 60000)
}

const bayStatusColors: Record<string, string> = {
  active: 'bg-green-500',
  idle: 'bg-gray-400',
  maintenance: 'bg-orange-500',
}

const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-blue-100 text-blue-800',
  in_progress: 'bg-purple-100 text-purple-800',
}
</script>

<template>
  <Head title="Staff Dashboard" />

  <StaffLayout>
    <div class="p-6 space-y-6">
      <!-- Page Header -->
      <div>
        <h1 class="text-3xl font-bold">Operations Dashboard</h1>
        <p class="text-muted-foreground mt-1">Manage queue, bays, and customer service</p>
      </div>

      <!-- Stats Overview -->
      <div class="grid gap-4 md:grid-cols-4">
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">Waiting in Queue</span>
            <ListOrdered class="w-5 h-5 text-orange-600" />
          </div>
          <div class="text-3xl font-bold">{{ stats.waitingQueue }}</div>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">Active Bays</span>
            <Waves class="w-5 h-5 text-green-600" />
          </div>
          <div class="text-3xl font-bold">{{ stats.activeBays }}</div>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">Idle Bays</span>
            <Waves class="w-5 h-5 text-gray-400" />
          </div>
          <div class="text-3xl font-bold">{{ stats.idleBays }}</div>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-muted-foreground">Completed Today</span>
            <CheckCircle class="w-5 h-5 text-blue-600" />
          </div>
          <div class="text-3xl font-bold">{{ stats.completedToday }}</div>
        </div>
      </div>

      <!-- Queue & Bays Grid -->
      <div class="grid gap-6 lg:grid-cols-2">
        <!-- Queue Management -->
        <div class="bg-card border rounded-lg">
          <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-xl font-bold flex items-center gap-2">
              <ListOrdered class="w-5 h-5" />
              Current Queue
            </h2>
            <Link
              :href="route('staff.queue.index')"
              class="text-sm text-primary hover:underline"
            >
              Manage Queue
            </Link>
          </div>

          <div v-if="queueEntries.length === 0" class="px-6 py-12 text-center">
            <ListOrdered class="w-12 h-12 mx-auto text-muted-foreground mb-3" />
            <p class="text-muted-foreground">No customers in queue</p>
          </div>

          <div v-else class="divide-y max-h-96 overflow-y-auto">
            <div
              v-for="entry in queueEntries"
              :key="entry.id"
              class="px-6 py-4 hover:bg-muted/50 transition-colors"
            >
              <div class="flex items-start gap-4">
                <div class="flex items-center justify-center w-10 h-10 bg-primary/10 rounded-full font-bold text-primary">
                  {{ entry.position }}
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-semibold">{{ entry.customer.name }}</div>
                  <div class="text-sm text-muted-foreground">
                    {{ entry.package.name }} • {{ entry.plate_number }}
                  </div>
                  <div class="text-xs text-muted-foreground mt-1">
                    Joined: {{ formatTime(entry.created_at) }}
                  </div>
                </div>
                <Link
                  :href="route('staff.queue.start', entry.id)"
                  method="post"
                  as="button"
                  class="flex items-center gap-1 px-3 py-1.5 bg-primary text-primary-foreground rounded-lg text-sm font-medium hover:bg-primary/90 transition-colors"
                >
                  <Play class="w-4 h-4" />
                  Start
                </Link>
              </div>
            </div>
          </div>
        </div>

        <!-- Bay Status Grid -->
        <div class="bg-card border rounded-lg">
          <div class="px-6 py-4 border-b flex items-center justify-between">
            <h2 class="text-xl font-bold flex items-center gap-2">
              <Waves class="w-5 h-5" />
              Bay Status
            </h2>
            <Link
              :href="route('staff.bays.index')"
              class="text-sm text-primary hover:underline"
            >
              Manage Bays
            </Link>
          </div>

          <div class="p-6">
            <div class="grid grid-cols-2 gap-3">
              <div
                v-for="bay in bays"
                :key="bay.id"
                class="p-4 border rounded-lg"
                :class="bay.status === 'active' ? 'border-green-500 bg-green-50' : ''"
              >
                <div class="flex items-center gap-2 mb-2">
                  <div :class="['w-3 h-3 rounded-full', bayStatusColors[bay.status]]"></div>
                  <span class="font-bold">{{ bay.name }}</span>
                </div>

                <div v-if="bay.currentWash" class="text-sm">
                  <div class="text-muted-foreground mb-1">{{ bay.currentWash.customer.name }}</div>
                  <div class="text-xs text-muted-foreground">
                    {{ bay.currentWash.package.name }}
                  </div>
                  <div class="text-xs font-medium text-green-600 mt-1">
                    {{ getElapsedMinutes(bay.currentWash.started_at) }} min
                  </div>
                </div>

                <div v-else class="text-sm text-muted-foreground capitalize">
                  {{ bay.status }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Today's Appointments -->
      <div class="bg-card border rounded-lg">
        <div class="px-6 py-4 border-b flex items-center justify-between">
          <h2 class="text-xl font-bold flex items-center gap-2">
            <Calendar class="w-5 h-5" />
            Today's Appointments
          </h2>
          <Link
            :href="route('staff.appointments.index')"
            class="text-sm text-primary hover:underline"
          >
            View All
          </Link>
        </div>

        <div v-if="todayAppointments.length === 0" class="px-6 py-12 text-center">
          <Calendar class="w-12 h-12 mx-auto text-muted-foreground mb-3" />
          <p class="text-muted-foreground">No appointments for today</p>
        </div>

        <div v-else class="divide-y">
          <div
            v-for="appointment in todayAppointments"
            :key="appointment.id"
            class="px-6 py-4 hover:bg-muted/50 transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                  <Clock class="w-4 h-4 text-muted-foreground" />
                  <span class="font-mono font-semibold">{{ formatTime(appointment.scheduled_at) }}</span>
                </div>
                <div>
                  <div class="flex items-center gap-2">
                    <User class="w-4 h-4 text-muted-foreground" />
                    <span class="font-semibold">{{ appointment.customer.name }}</span>
                  </div>
                  <div class="flex items-center gap-2 mt-1 text-sm text-muted-foreground">
                    <Package class="w-4 h-4" />
                    <span>{{ appointment.package.name }} • {{ appointment.plate_number }}</span>
                  </div>
                </div>
              </div>
              <span
                :class="['px-3 py-1 text-xs font-medium rounded-full', statusColors[appointment.status]]"
              >
                {{ appointment.status }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </StaffLayout>
</template>
