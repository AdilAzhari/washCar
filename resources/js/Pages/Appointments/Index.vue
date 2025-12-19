<script setup lang="ts">
import { computed } from 'vue'
import { Head, Link, router, usePage } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ManagerLayout from '@/Layouts/ManagerLayout.vue'
import { Calendar, Plus, Clock, MapPin, Package, CheckCircle, X, User } from 'lucide-vue-next'
import { Card, CardContent } from '@/Components/ui'

const page = usePage()
const userRole = computed(() => (page.props.auth as any)?.user?.role || 'admin')

const Layout = computed(() => {
  return userRole.value === 'manager' ? ManagerLayout : AuthenticatedLayout
})

const getRouteName = (routeName: string) => {
  return `${userRole.value}.${routeName}`
}

const props = defineProps<{
  appointments: {
    data: Array<{
      id: number
      scheduled_at: string
      branch: { id: number; name: string; code: string }
      customer?: { id: number; name: string }
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
    date?: string
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
  router.get(route(getRouteName('appointments.index')), { status, date: props.filters.date }, { preserveState: true })
}

const confirmAppointment = (appointmentId: number) => {
  router.post(route(getRouteName('appointments.confirm'), appointmentId))
}

const startAppointment = (appointmentId: number) => {
  router.post(route(getRouteName('appointments.start'), appointmentId))
}

const completeAppointment = (appointmentId: number) => {
  router.post(route(getRouteName('appointments.complete'), appointmentId))
}

const markNoShow = (appointmentId: number) => {
  if (confirm('Mark this appointment as no-show?')) {
    router.post(route(getRouteName('appointments.no-show'), appointmentId))
  }
}
</script>

<template>
  <Head title="Appointments" />

  <component :is="Layout">
    <template #header>
      <div class="flex items-center justify-between">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Appointments</h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="space-y-6">
          <!-- Header -->
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-2xl font-bold">Appointment Management</h1>
              <p class="text-muted-foreground mt-1">Manage and track all appointments</p>
            </div>
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
          <div v-if="appointments.data.length === 0">
            <Card class="bg-muted/30">
              <CardContent class="p-12 text-center">
                <Calendar class="w-12 h-12 text-muted-foreground mx-auto mb-4" />
                <h3 class="text-lg font-semibold mb-2">No appointments found</h3>
                <p class="text-muted-foreground">
                  {{ filters.status ? 'Try a different filter' : 'No appointments scheduled yet' }}
                </p>
              </CardContent>
            </Card>
          </div>

          <div v-else class="space-y-4">
            <Card
              v-for="appointment in appointments.data"
              :key="appointment.id"
              class="hover:shadow-md transition-shadow"
            >
              <CardContent class="p-6">
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
                        <User class="w-4 h-4" />
                        <span>{{ appointment.customer?.name || 'Walk-in' }}</span>
                      </div>
                      <div class="flex items-center gap-2 text-muted-foreground">
                        <Package class="w-4 h-4" />
                        <span>{{ appointment.vehicle_type }} • {{ appointment.plate_number }}</span>
                      </div>
                    </div>

                    <div v-if="appointment.special_requests" class="mt-3 p-3 bg-muted rounded-lg">
                      <p class="text-sm text-muted-foreground">
                        <span class="font-medium text-foreground">Special Requests:</span> {{ appointment.special_requests }}
                      </p>
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-3 flex-wrap">
                  <button
                    v-if="appointment.status === 'pending'"
                    type="button"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors"
                    @click="confirmAppointment(appointment.id)"
                  >
                    Confirm
                  </button>

                  <button
                    v-if="appointment.status === 'confirmed'"
                    type="button"
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg text-sm font-medium hover:bg-purple-700 transition-colors"
                    @click="startAppointment(appointment.id)"
                  >
                    Start Service
                  </button>

                  <button
                    v-if="appointment.status === 'in_progress'"
                    type="button"
                    class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors"
                    @click="completeAppointment(appointment.id)"
                  >
                    Complete
                  </button>

                  <button
                    v-if="['pending', 'confirmed'].includes(appointment.status)"
                    type="button"
                    class="px-4 py-2 border border-red-200 text-red-600 rounded-lg text-sm font-medium hover:bg-red-50 transition-colors"
                    @click="markNoShow(appointment.id)"
                  >
                    Mark No-Show
                  </button>

                  <Link
                    :href="route(getRouteName('appointments.show'), appointment.id)"
                    class="px-4 py-2 bg-muted text-foreground rounded-lg text-sm font-medium hover:bg-muted/80 transition-colors"
                  >
                    View Details
                  </Link>
                </div>
              </CardContent>
            </Card>
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
      </div>
    </div>
  </component>
</template>
