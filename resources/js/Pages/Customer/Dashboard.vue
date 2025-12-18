<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { Calendar, History, Star, Clock, MapPin, Package, AlertCircle } from 'lucide-vue-next'

defineProps<{
  loyaltyPoints: {
    points: number
    lifetime_points: number
    tier: string
  }
  upcomingAppointments: Array<{
    id: number
    scheduled_at: string
    branch: { name: string; code: string }
    package: { name: string; price: number }
    status: string
  }>
  recentWashes: Array<{
    id: number
    completed_at: string
    branch: { name: string }
    package: { name: string }
    total_amount: number
    status: string
  }>
  activeQueue: {
    id: number
    position: number
    branch: { name: string; code: string }
    status: string
  } | null
}>()

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
  })
}

const statusColors: Record<string, string> = {
  pending: 'bg-yellow-100 text-yellow-800',
  confirmed: 'bg-blue-100 text-blue-800',
  in_progress: 'bg-purple-100 text-purple-800',
  completed: 'bg-green-100 text-green-800',
  cancelled: 'bg-red-100 text-red-800',
}
</script>

<template>
  <Head title="Dashboard" />

  <CustomerLayout>
    <div class="p-6 space-y-6">
      <!-- Welcome Section -->
      <div>
        <h1 class="text-3xl font-bold">Welcome Back!</h1>
        <p class="text-muted-foreground mt-1">Here's what's happening with your account</p>
      </div>

      <!-- Active Queue Alert -->
      <div
        v-if="activeQueue"
        class="bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg p-6 shadow-lg"
      >
        <div class="flex items-start gap-4">
          <div class="flex-shrink-0">
            <AlertCircle class="w-8 h-8" />
          </div>
          <div class="flex-1">
            <h3 class="text-lg font-bold mb-1">You're in the Queue!</h3>
            <p class="mb-3 opacity-90">Position: #{{ activeQueue.position }} at {{ activeQueue.branch.name }}</p>
            <Link
              :href="`/queue/status/${activeQueue.branch.code}/${activeQueue.id}`"
              class="inline-flex items-center px-4 py-2 bg-white text-orange-600 rounded-lg font-medium hover:bg-orange-50 transition-colors"
            >
              View Queue Status
            </Link>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="grid gap-4 md:grid-cols-3">
        <Link
          :href="route('customer.appointments.create')"
          class="flex items-center gap-4 p-6 bg-card border rounded-lg hover:shadow-md transition-shadow"
        >
          <div class="flex items-center justify-center w-12 h-12 bg-primary/10 rounded-lg">
            <Calendar class="w-6 h-6 text-primary" />
          </div>
          <div>
            <h3 class="font-semibold">Book Appointment</h3>
            <p class="text-sm text-muted-foreground">Schedule your next wash</p>
          </div>
        </Link>

        <Link
          :href="route('customer.history')"
          class="flex items-center gap-4 p-6 bg-card border rounded-lg hover:shadow-md transition-shadow"
        >
          <div class="flex items-center justify-center w-12 h-12 bg-blue-500/10 rounded-lg">
            <History class="w-6 h-6 text-blue-600" />
          </div>
          <div>
            <h3 class="font-semibold">Wash History</h3>
            <p class="text-sm text-muted-foreground">View past services</p>
          </div>
        </Link>

        <Link
          :href="route('customer.loyalty')"
          class="flex items-center gap-4 p-6 bg-card border rounded-lg hover:shadow-md transition-shadow"
        >
          <div class="flex items-center justify-center w-12 h-12 bg-yellow-500/10 rounded-lg">
            <Star class="w-6 h-6 text-yellow-600" />
          </div>
          <div>
            <h3 class="font-semibold">Rewards</h3>
            <p class="text-sm text-muted-foreground">Check your points</p>
          </div>
        </Link>
      </div>

      <!-- Upcoming Appointments -->
      <div class="bg-card border rounded-lg">
        <div class="px-6 py-4 border-b">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">Upcoming Appointments</h2>
            <Link
              :href="route('customer.appointments.index')"
              class="text-sm text-primary hover:underline"
            >
              View All
            </Link>
          </div>
        </div>

        <div v-if="upcomingAppointments.length === 0" class="px-6 py-12 text-center">
          <Calendar class="w-12 h-12 mx-auto text-muted-foreground mb-3" />
          <p class="text-muted-foreground mb-4">No upcoming appointments</p>
          <Link
            :href="route('customer.appointments.create')"
            class="inline-flex items-center px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
          >
            Book Your First Appointment
          </Link>
        </div>

        <div v-else class="divide-y">
          <div
            v-for="appointment in upcomingAppointments"
            :key="appointment.id"
            class="px-6 py-4 hover:bg-muted/50 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-2 mb-2">
                  <Package class="w-4 h-4 text-muted-foreground" />
                  <span class="font-semibold">{{ appointment.package.name }}</span>
                  <span
                    :class="['px-2 py-0.5 text-xs font-medium rounded', statusColors[appointment.status]]"
                  >
                    {{ appointment.status }}
                  </span>
                </div>
                <div class="flex items-center gap-4 text-sm text-muted-foreground">
                  <div class="flex items-center gap-1">
                    <Clock class="w-4 h-4" />
                    {{ formatDate(appointment.scheduled_at) }}
                  </div>
                  <div class="flex items-center gap-1">
                    <MapPin class="w-4 h-4" />
                    {{ appointment.branch.name }}
                  </div>
                </div>
              </div>
              <Link
                :href="route('customer.appointments.show', appointment.id)"
                class="text-sm text-primary hover:underline"
              >
                View Details
              </Link>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Washes -->
      <div class="bg-card border rounded-lg">
        <div class="px-6 py-4 border-b">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold">Recent Services</h2>
            <Link
              :href="route('customer.history')"
              class="text-sm text-primary hover:underline"
            >
              View All
            </Link>
          </div>
        </div>

        <div v-if="recentWashes.length === 0" class="px-6 py-12 text-center">
          <History class="w-12 h-12 mx-auto text-muted-foreground mb-3" />
          <p class="text-muted-foreground">No wash history yet</p>
        </div>

        <div v-else class="divide-y">
          <div
            v-for="wash in recentWashes"
            :key="wash.id"
            class="px-6 py-4"
          >
            <div class="flex items-center justify-between">
              <div class="flex-1">
                <div class="font-semibold mb-1">{{ wash.package.name }}</div>
                <div class="flex items-center gap-4 text-sm text-muted-foreground">
                  <span>{{ formatDate(wash.completed_at) }}</span>
                  <span>{{ wash.branch.name }}</span>
                </div>
              </div>
              <div class="text-right">
                <div class="font-semibold">${{ wash.total_amount.toFixed(2) }}</div>
                <div class="text-sm text-muted-foreground">
                  +{{ Math.floor(wash.total_amount) }} pts
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </CustomerLayout>
</template>
