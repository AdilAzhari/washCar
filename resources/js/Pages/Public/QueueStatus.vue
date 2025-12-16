<script setup lang="ts">
import { Head } from '@inertiajs/vue3'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/Components/ui'
import { Badge } from '@/Components/ui/Badge.vue'
import ApplicationLogo from '@/Components/ApplicationLogo.vue'
import { ref, onMounted, onUnmounted } from 'vue'

interface Branch {
  id: number
  name: string
  code: string
  address: string
}

interface Customer {
  id: number
  name: string
  phone: string
}

interface Package {
  id: number
  name: string
  price: number
  duration: number
}

interface QueueEntry {
  id: number
  position: number
  status: string
  plate_number: string
  joined_at: string
  customer: Customer
  package: Package | null
}

interface Stats {
  nowServing: number
  totalWaiting: number
}

const props = defineProps<{
  branch: Branch
  queueEntry: QueueEntry
  stats: Stats
}>()

const currentTime = ref(new Date())

// Update time every second
let timeInterval: number | undefined

onMounted(() => {
  timeInterval = window.setInterval(() => {
    currentTime.value = new Date()
  }, 1000)

  // Auto-refresh every 30 seconds
  window.setInterval(() => {
    window.location.reload()
  }, 30000)
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})

const getStatusColor = (status: string) => {
  const colors: Record<string, string> = {
    waiting: 'bg-yellow-100 text-yellow-800 border-yellow-300',
    in_progress: 'bg-blue-100 text-blue-800 border-blue-300',
    completed: 'bg-green-100 text-green-800 border-green-300',
    cancelled: 'bg-red-100 text-red-800 border-red-300'
  }
  return colors[status] || 'bg-gray-100 text-gray-800 border-gray-300'
}

const getStatusText = (status: string) => {
  const texts: Record<string, string> = {
    waiting: 'Waiting in Queue',
    in_progress: 'Being Served',
    completed: 'Completed',
    cancelled: 'Cancelled'
  }
  return texts[status] || status
}

const formatTime = (dateString: string) => {
  const date = new Date(dateString)
  return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
}

const getWaitingTime = () => {
  const joined = new Date(props.queueEntry.joined_at)
  const diff = currentTime.value.getTime() - joined.getTime()
  const minutes = Math.floor(diff / 60000)
  return minutes
}

const getPositionAhead = () => {
  return Math.max(0, props.queueEntry.position - props.stats.nowServing)
}
</script>

<template>
  <Head title="Queue Status" />

  <div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-green-50">
    <!-- Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
      <div class="max-w-4xl mx-auto px-4 py-6">
        <div class="flex items-center gap-4">
          <ApplicationLogo class="h-12 w-auto" />
          <div>
            <h1 class="text-2xl font-bold text-gray-900">WashyWashy Pro</h1>
            <p class="text-sm text-gray-600">{{ branch.name }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 py-8">
      <!-- Queue Number Card -->
      <Card class="mb-6 bg-gradient-to-r from-blue-500 to-green-500 text-white border-0">
        <CardContent class="pt-8 pb-8">
          <div class="text-center">
            <p class="text-blue-100 text-sm uppercase tracking-wide mb-2">Your Queue Number</p>
            <div class="text-7xl font-bold mb-2">{{ queueEntry.position }}</div>
            <Badge :class="['text-lg px-4 py-2 mt-4', getStatusColor(queueEntry.status)]">
              {{ getStatusText(queueEntry.status) }}
            </Badge>
          </div>
        </CardContent>
      </Card>

      <!-- Status Grid -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Now Serving -->
        <Card class="bg-white">
          <CardHeader class="pb-3">
            <CardDescription>Now Serving</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="text-4xl font-bold text-blue-600">#{{ stats.nowServing || '-' }}</div>
          </CardContent>
        </Card>

        <!-- People Ahead -->
        <Card class="bg-white">
          <CardHeader class="pb-3">
            <CardDescription>People Ahead</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="text-4xl font-bold text-orange-600">{{ getPositionAhead() }}</div>
          </CardContent>
        </Card>

        <!-- Waiting Time -->
        <Card class="bg-white">
          <CardHeader class="pb-3">
            <CardDescription>Waiting Time</CardDescription>
          </CardHeader>
          <CardContent>
            <div class="text-4xl font-bold text-green-600">{{ getWaitingTime() }}<span class="text-2xl">m</span></div>
          </CardContent>
        </Card>
      </div>

      <!-- Customer Details -->
      <Card class="mb-6">
        <CardHeader>
          <CardTitle>Your Details</CardTitle>
        </CardHeader>
        <CardContent>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <p class="text-sm text-gray-600 mb-1">Customer Name</p>
              <p class="font-semibold text-gray-900">{{ queueEntry.customer.name }}</p>
            </div>

            <div>
              <p class="text-sm text-gray-600 mb-1">Phone Number</p>
              <p class="font-semibold text-gray-900">{{ queueEntry.customer.phone }}</p>
            </div>

            <div>
              <p class="text-sm text-gray-600 mb-1">Vehicle Plate</p>
              <p class="font-semibold text-gray-900">{{ queueEntry.plate_number }}</p>
            </div>

            <div>
              <p class="text-sm text-gray-600 mb-1">Joined At</p>
              <p class="font-semibold text-gray-900">{{ formatTime(queueEntry.joined_at) }}</p>
            </div>

            <div v-if="queueEntry.package" class="md:col-span-2">
              <p class="text-sm text-gray-600 mb-1">Selected Package</p>
              <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                <div>
                  <p class="font-semibold text-gray-900">{{ queueEntry.package.name }}</p>
                  <p class="text-sm text-gray-600">{{ queueEntry.package.duration }} minutes</p>
                </div>
                <p class="text-xl font-bold text-blue-600">${{ queueEntry.package.price }}</p>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Instructions based on status -->
      <Card v-if="queueEntry.status === 'waiting'" class="bg-yellow-50 border-yellow-200">
        <CardContent class="pt-6">
          <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-yellow-800">
              <p class="font-semibold mb-2">Please Wait</p>
              <p>{{ getPositionAhead() > 0 ? `There are ${getPositionAhead()} people ahead of you.` : 'You\'re next! Please be ready.' }}</p>
              <p class="mt-2">Stay nearby and keep an eye on the queue number. We'll call you when it's your turn.</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card v-else-if="queueEntry.status === 'in_progress'" class="bg-blue-50 border-blue-200">
        <CardContent class="pt-6">
          <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div class="text-sm text-blue-800">
              <p class="font-semibold mb-2">Your Vehicle is Being Washed!</p>
              <p>Please proceed to the designated bay. Our team is currently servicing your vehicle.</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <Card v-else-if="queueEntry.status === 'completed'" class="bg-green-50 border-green-200">
        <CardContent class="pt-6">
          <div class="flex items-start gap-3">
            <svg class="w-6 h-6 text-green-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div class="text-sm text-green-800">
              <p class="font-semibold mb-2">Service Completed!</p>
              <p>Thank you for choosing WashyWashy Pro. Your vehicle is ready for pickup.</p>
              <p class="mt-2">We hope to see you again soon!</p>
            </div>
          </div>
        </CardContent>
      </Card>

      <!-- Auto Refresh Info -->
      <div class="mt-6 text-center">
        <p class="text-sm text-gray-500">
          <svg class="w-4 h-4 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
          </svg>
          This page refreshes automatically every 30 seconds
        </p>
      </div>
    </div>
  </div>
</template>
