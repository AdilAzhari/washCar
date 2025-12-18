<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3'
import CustomerLayout from '@/Layouts/CustomerLayout.vue'
import { History, MapPin, Package, Calendar, DollarSign, Star } from 'lucide-vue-next'

defineProps<{
  washes: {
    data: Array<{
      id: number
      completed_at: string
      branch: { id: number; name: string }
      package: { id: number; name: string; price: number }
      bay?: { name: string }
      total_amount: number
      plate_number?: string
      vehicle_type?: string
    }>
    links: any
    meta: any
  }
  totalSpent: number
  totalWashes: number
  totalPointsEarned: number
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
</script>

<template>
  <Head title="Wash History" />

  <CustomerLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div>
        <h1 class="text-3xl font-bold">Wash History</h1>
        <p class="text-muted-foreground mt-1">Your complete service history</p>
      </div>

      <!-- Stats Cards -->
      <div class="grid gap-4 md:grid-cols-3">
        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <History class="w-5 h-5 text-blue-600" />
            <span class="text-sm font-medium text-muted-foreground">Total Washes</span>
          </div>
          <div class="text-3xl font-bold">{{ totalWashes }}</div>
          <p class="text-xs text-muted-foreground mt-1">All time</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <DollarSign class="w-5 h-5 text-green-600" />
            <span class="text-sm font-medium text-muted-foreground">Total Spent</span>
          </div>
          <div class="text-3xl font-bold">RM {{ totalSpent.toFixed(2) }}</div>
          <p class="text-xs text-muted-foreground mt-1">Lifetime</p>
        </div>

        <div class="bg-card border rounded-lg p-6">
          <div class="flex items-center gap-2 mb-2">
            <Star class="w-5 h-5 text-yellow-600" />
            <span class="text-sm font-medium text-muted-foreground">Points Earned</span>
          </div>
          <div class="text-3xl font-bold">{{ totalPointsEarned.toLocaleString() }}</div>
          <p class="text-xs text-muted-foreground mt-1">Total points</p>
        </div>
      </div>

      <!-- Wash History List -->
      <div class="bg-card border rounded-lg">
        <div class="px-6 py-4 border-b">
          <h2 class="text-xl font-bold">Service History</h2>
        </div>

        <div v-if="washes.data.length === 0" class="px-6 py-12 text-center">
          <History class="w-16 h-16 mx-auto text-muted-foreground mb-4" />
          <h3 class="text-lg font-semibold mb-2">No wash history yet</h3>
          <p class="text-muted-foreground mb-6">
            Start by booking your first appointment or joining the queue
          </p>
          <Link
            :href="route('customer.appointments.create')"
            class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-primary-foreground rounded-lg hover:bg-primary/90 transition-colors"
          >
            <Calendar class="w-5 h-5" />
            Book Appointment
          </Link>
        </div>

        <div v-else class="divide-y">
          <div
            v-for="wash in washes.data"
            :key="wash.id"
            class="p-6 hover:bg-muted/50 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex-1">
                <div class="flex items-center gap-3 mb-3">
                  <Package class="w-5 h-5 text-primary" />
                  <h3 class="font-bold text-lg">{{ wash.package.name }}</h3>
                </div>

                <div class="grid gap-2 sm:grid-cols-2 text-sm mb-3">
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <Calendar class="w-4 h-4" />
                    <span>{{ formatDate(wash.completed_at) }} at {{ formatTime(wash.completed_at) }}</span>
                  </div>
                  <div class="flex items-center gap-2 text-muted-foreground">
                    <MapPin class="w-4 h-4" />
                    <span>{{ wash.branch.name }}</span>
                  </div>
                  <div v-if="wash.bay" class="flex items-center gap-2 text-muted-foreground">
                    <span class="font-medium">Bay:</span>
                    <span>{{ wash.bay.name }}</span>
                  </div>
                  <div v-if="wash.vehicle_type" class="flex items-center gap-2 text-muted-foreground">
                    <span>{{ wash.vehicle_type }} • {{ wash.plate_number }}</span>
                  </div>
                </div>

                <div class="flex items-center gap-4">
                  <div class="flex items-center gap-2">
                    <DollarSign class="w-4 h-4 text-green-600" />
                    <span class="font-bold text-green-600">RM {{ wash.total_amount.toFixed(2) }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <Star class="w-4 h-4 text-yellow-600" />
                    <span class="font-semibold text-yellow-600">
                      +{{ Math.floor(wash.total_amount) }} pts
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="washes.links.length > 3" class="flex items-center justify-center gap-2">
        <template v-for="(link, index) in washes.links" :key="index">
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
