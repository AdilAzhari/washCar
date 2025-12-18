<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { Calendar, Plus, Clock, MapPin, Package, CheckCircle, X } from 'lucide-vue-next'

const props = defineProps<{
  appointments: {
    data: Array<{
      id: number
      scheduled_at: string
      branch: { id: number; name: string; code: string }
      package: { id: number; name: string; price: number }
      plate_number: string
      vehicle_type: string
      status: string
      special_requests?: string
    }>
    links: any
    meta: any
  }
  filters: {
    status?: string
  }
}>()

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
  })
}

const formatTime = (dateString: string) => {
  return new Date(dateString).toLocaleTimeString('en-US', {
    hour: 'numeric',
    minute: '2-digit',
  })
}

const statusConfig: Record<string, { label: string; color: string; icon: any }> = {
  pending: {
    label: 'Pending',
    color: 'bg-yellow-100 text-yellow-800 border-yellow-200',
    icon: Clock,
  },
  confirmed: {
    label: 'Confirmed',
    color: 'bg-blue-100 text-blue-800 border-blue-200',
    icon: CheckCircle,
  },
  in_progress: {
    label: 'In Progress',
    color: 'bg-purple-100 text-purple-800 border-purple-200',
    icon: Clock,
  },
  completed: {
    label: 'Completed',
    color: 'bg-green-100 text-green-800 border-green-200',
    icon: CheckCircle,
  },
  cancelled: {
    label: 'Cancelled',
    color: 'bg-red-100 text-red-800 border-red-200',
    icon: X,
  },
  no_show: {
    label: 'No Show',
    color: 'bg-gray-100 text-gray-800 border-gray-200',
    icon: X,
  },
}

const filterStatus = (status?: string) => {
  router.get(route('customer.appointments.index'), { status }, { preserveState: true })
}

const canCancel = (appointment: any) => {
  return ['pending', 'confirmed'].includes(appointment.status)
}

const cancelAppointment = (appointmentId: number) => {
  if (confirm('Are you sure you want to cancel this appointment?')) {
    router.delete(route('customer.appointments.destroy', appointmentId))
  }
}
</script>

<template>
  <Head title="My Appointments" />

  <CustomerLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold">My Appointments</h1>
          <p class="text-muted-foreground mt-1">Manage your scheduled car washes</p>
        </div>
        <Link
          :href="route('customer.appointments.create')"
          class="flex items-center gap-2 px-4 py-2 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
        >
          <Plus class="w-5 h-5" />
          Book Appointment
        </Link>
      </div>

      <!-- Filters -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2">
        <button
          @click="filterStatus()"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap',
            !filters.status
              ? 'bg-primary text-primary-foreground'
              : 'bg-muted text-muted-foreground hover:bg-muted/80',
          ]"
        >
          All
        </button>
        <button
          v-for="(config, status) in statusConfig"
          :key="status"
          @click="filterStatus(status)"
          :class="[
            'px-4 py-2 rounded-lg text-sm font-medium transition-colors whitespace-nowrap',
            filters.status === status
              ? 'bg-primary text-primary-foreground'
              : 'bg-muted text-muted-foreground hover:bg-muted/80',
          ]"
        >
          {{ config.label }}
        </button>
      </div>

      <!-- Appointments List -->
      <div v-if="appointments.data.length === 0" class="bg-card border rounded-lg p-12 text-center">
        <Calendar class="w-16 h-16 mx-auto text-muted-foreground mb-4" />
        <h3 class="text-lg font-semibold mb-2">No appointments found</h3>
        <p class="text-muted-foreground mb-6">
          {{ filters.status ? 'Try a different filter or' : 'Start by' }} booking your first appointment
        </p>
        <Link
          :href="route('customer.appointments.create')"
          class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
        >
          <Plus class="w-5 h-5" />
          Book Appointment
        </Link>
      </div>

      <div v-else class="space-y-4">
        <div
          v-for="appointment in appointments.data"
          :key="appointment.id"
          class="bg-card border rounded-lg overflow-hidden hover:shadow-md transition-shadow"
        >
          <div class="p-6">
            <div class="flex items-start justify-between mb-4">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                  <h3 class="text-xl font-bold">{{ appointment.package.name }}</h3>
                  <div
                    :class="[
                      'px-3 py-1 rounded-full text-xs font-medium border',
                      statusConfig[appointment.status].color,
                    ]"
                  >
                    {{ statusConfig[appointment.status].label }}
                  </div>
                </div>

                <div class="grid gap-2 sm:grid-cols-2 text-sm">
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <Clock class="w-4 h-4" />
                    <span>{{ formatDate(appointment.scheduled_at) }} at {{ formatTime(appointment.scheduled_at) }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <MapPin class="w-4 h-4" />
                    <span>{{ appointment.branch.name }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <Package class="w-4 h-4" />
                    <span>{{ appointment.vehicle_type }} • {{ appointment.plate_number }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <span class="font-semibold text-foreground">RM {{ appointment.package.price.toFixed(2) }}</span>
                  </div>
                </div>

                <div v-if="appointment.special_requests" class="mt-3 p-3 bg-muted rounded-lg">
                  <p class="text-sm text-muted-foreground">
                    <span class="font-medium text-foreground">Special Requests:</span> {{ appointment.special_requests }}
                  </p>
                </div>
              </div>
            </div>

            <div class="flex items-center gap-3">
              <Link
                :href="route('customer.appointments.show', appointment.id)"
                class="flex-1 sm:flex-initial px-4 py-2 bg-muted text-foreground rounded-lg text-sm font-medium hover:bg-muted/80 transition-colors text-center"
              >
                View Details
              </Link>

              <button
                v-if="canCancel(appointment)"
                type="button"
                class="px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors"
                @click="cancelAppointment(appointment.id)"
              >
                Cancel
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="appointments.links.length > 3" class="flex items-center justify-center gap-2">
        <template v-for="(link, index) in appointments.links" :key="index">
          <Link
            v-if="link.url"
            :href="link.url"
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
              link.active
                ? 'bg-primary text-primary-foreground'
                : 'bg-muted text-muted-foreground hover:bg-muted/80',
            ]"
            v-html="link.label"
          />
          <span
            v-else
            :class="[
              'px-4 py-2 rounded-lg text-sm font-medium',
              'bg-muted/50 text-muted-foreground/50 cursor-not-allowed',
            ]"
            v-html="link.label"
          />
        </template>
      </div>
    </div>
  </CustomerLayout>
</template>
